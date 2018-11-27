<?php
/**
 * Created by PhpStorm.
 * User: rayndeng
 * Date: 27/11/18
 * Time: 5:12 PM
 */

namespace TimeHunter\LaravelEmailChangeVerification\Repositories;



use Inkstation\Customer\Model\Customer;

/**
 * Class CustomerRepo
 * @package Inkstation\Customer\Repositories
 */
class CustomerRepo
{

    /**
     * @param $email
     * @return mixed
     */
    public function ifEmailExists($email)
    {
        return Customer::where('customers_email_address', $email)->exists();
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function getCustomerByCustomerId($customerId)
    {
        return Customer::where('customers_id', $customerId)->first();
    }

    /**
     * @param Customer $customer
     * @param $newEmailAddress
     * @return bool
     */
    public function updateCustomerModelWithNewEmail(Customer $customer, $newEmailAddress)
    {
        $customer->customers_email_address = $newEmailAddress;
        return $customer->save();
    }

}
