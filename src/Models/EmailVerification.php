<?php

namespace Inkstation\Customer\Model\DB;


/**
 * @property integer $customer_id
 * @property int $id
 * @property string $token
 * @property string $new_email
 * @property string $old_email
 * @property string $is_used
 * @property string $created_at
 * @property integer $updated_at
 */
class EmailVerification extends InkstationTable
{
    protected $table = 'email_verification';
    protected $guarded = [];
    public $timestamps = true;


}