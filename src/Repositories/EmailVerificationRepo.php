<?php

namespace Inkstation\Customer\Repositories;

use Inkstation\Customer\Model\DB\EmailVerification;

/**
 * Class EmailVerificationRepo
 * @package Inkstation\Customer\Repositories
 */
class EmailVerificationRepo
{
    /**
     * @param $customerId
     * @param $oldEmail
     * @param $newEmail
     * @return EmailVerification
     */
    public function createRecord($customerId, $oldEmail, $newEmail)
    {

        $exist = EmailVerification::where('customer_id', $customerId)
            ->where('old_email', $oldEmail)
            ->where('new_email', $newEmail)
            ->where('is_used', 0)
            ->first();
        if ($exist) {
            return $exist;
        }
        $emailVerification = new EmailVerification();
        $emailVerification->customer_id = $customerId;


        $token = md5($customerId . strtotime('now') . str_random(10));
        $emailVerification->token = $token;
        $emailVerification->old_email = $oldEmail;
        $emailVerification->new_email = $newEmail;
        $emailVerification->is_used = false;
        $emailVerification->save();
        return $emailVerification;
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function deleteAllUnusedRecords($customerId)
    {
        return EmailVerification::where('customer_id', $customerId)
            ->where('is_used', 0)
            ->delete();
    }

    /**
     * @param $customerId
     * @param $token
     * @return EmailVerification
     */
    public function getValidRecord($customerId, $token)
    {
        return EmailVerification::where('token', $token)
            ->where('customer_id', $customerId)
            ->where('is_used', 0)
            ->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        return EmailVerification::where('id', $id)->delete();
    }

    /**
     * @param EmailVerification $record
     * @return bool
     */
    public function updateUsedToken(EmailVerification $record)
    {
        $record->is_used = 1;
        return $record->save();
    }
}