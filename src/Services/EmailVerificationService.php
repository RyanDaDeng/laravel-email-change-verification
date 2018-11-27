<?php

namespace Inkstation\Customer\Services;


use Inkstation\Customer\Model\DB\EmailVerification;
use Inkstation\Customer\Repositories\EmailVerificationRepo;
use Illuminate\Support\Facades\Crypt;
use \Inkstation\Customer\Model\Customer;
use \Inkstation\Customer\Services\Customer as CustomerRepo;
/**
 * Class EmailVerificationService
 * @package Inkstation\Customer\Services
 */
class EmailVerificationService
{

    /**
     * @var EmailVerificationRepo
     */
    private $emailVerificationRepo;

    /**
     * @var \Inkstation\Customer\Services\Customer
     */
    private $customerRepo;

    /**
     * Not found token record
     */
    const ERROR_RECORD_NOT_FOUND = 'ERROR-EMAIL-VERIFICATION-100';
    /**
     * New email address has been used before verify
     */
    const ERROR_EMAIL_EXISTS = 'ERROR-EMAIL-VERIFICATION-200';
    /**
     * Email from token is not valid
     */
    const ERROR_TOKEN_EMAIL_INVALID = 'ERROR-EMAIL-VERIFICATION-300';
    /**
     * Token is invalid
     */
    const ERROR_TOKEN_NOT_MATCH = 'ERROR-EMAIL-VERIFICATION-400';
    /**
     * System error when updating record and email
     */
    const ERROR_SYSTEM_NOT_UPDATE = 'ERROR-EMAIL-VERIFICATION-500';
    /**
     * Success
     */
    const SUCCESS_TOKEN = 'Successfully updated';

    /**
     * EmailVerificationService constructor.
     * @param EmailVerificationRepo $emailVerificationRepo
     * @param CustomerRepo $customerRepo
     */
    public function __construct(EmailVerificationRepo $emailVerificationRepo, CustomerRepo $customerRepo)
    {
        $this->emailVerificationRepo = $emailVerificationRepo;
        $this->customerRepo = $customerRepo;
    }

    /**
     * @param $token
     * @param $customerId
     * @return string
     */
    public function cryptEmailVerification($token, $customerId)
    {
        $payload = [
            'token' => $token,
            'cid' => $customerId
        ];

        $payload = json_encode($payload);
        return Crypt::encrypt($payload);
    }


    /**
     * @param $payload
     * @return \Inkstation\Customer\Model\DB\EmailVerification|null
     */
    public function getRecordByTokenPayload($payload)
    {
        try {
            $decrypted = Crypt::decrypt($payload);
            $payloadArray = json_decode($decrypted, 1);
            return $this->emailVerificationRepo->getValidRecord(
                $payloadArray['cid'],
                $payloadArray['token']
            );
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * @param $token
     * @return EmailVerificationResponse
     */
    public function verify($token)
    {
        $record = $this->getRecordByTokenPayload($token);

        // if record exists
        if (!$record) {
            return EmailVerificationResponse::error(self::ERROR_RECORD_NOT_FOUND);
        }

        // if new email already exists
        if ($this->customerRepo->ifEmailExists($record->new_email)) {
            //if used, then delete record
            $this->emailVerificationRepo->deleteRecord($record->id);
            return EmailVerificationResponse::error(self::ERROR_EMAIL_EXISTS);
        }

        // check if they exist
        $customer = $this->customerRepo->getCustomerByCustomerId($record->customer_id);
        if (!$customer) {
            return EmailVerificationResponse::error(self::ERROR_TOKEN_EMAIL_INVALID);
        }

        // check if customer matches to record
        if (!$this->validateToken($record, $customer)) {
            return EmailVerificationResponse::error(self::ERROR_TOKEN_NOT_MATCH);
        }

        // if everything right, update
        if ($this->updateEmailAndToken($record, $customer)) {
            return EmailVerificationResponse::success($record->new_email);
        } else {
            return EmailVerificationResponse::error(self::ERROR_SYSTEM_NOT_UPDATE);
        }
    }

    /**
     * @param EmailVerification $record
     * @param Customer $customer
     * @return bool
     */
    public function updateEmailAndToken(EmailVerification $record, Customer $customer)
    {
        try {
            $this->customerRepo->updateCustomerModelWithNewEmail($customer, $record->new_email);
            $this->emailVerificationRepo->updateUsedToken($record);
            $this->emailVerificationRepo->deleteAllUnusedRecords($record->customer_id);
            return true;
        } catch (\Exception $e) {
            //todo log
            return false;
        }
    }

    /**
     * @param EmailVerification $record
     * @param Customer $customer
     * @return bool
     */
    public function validateToken(EmailVerification $record, Customer $customer)
    {
        return $record->old_email === $customer->customers_email_address
            && $record->customer_id === $customer->customers_id
            && !$record->is_used;
    }


    /**
     * @param $customerId
     * @param $oldEmail
     * @param $newEmail
     * @return EmailVerification
     */
    public function createRecord($customerId, $oldEmail, $newEmail)
    {
        return $this->emailVerificationRepo->createRecord($customerId, $oldEmail, $newEmail);
    }
}