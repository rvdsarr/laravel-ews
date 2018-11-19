<?php

namespace andres3210\laraews;

use andres3210\laraews\ExchangeClient;
use andres3210\laraews\models\ExchangeSubscription;

use \jamesiarmes\PhpEws\Type\SendNotificationResultType;

class NotificationRequest {

    const WSDL_NOTIFICATION_SERVICE = '{ROOT}/src/assets/NotificationService.wsdl';

    public function SendNotification($args){

        $res = false;
        if( isset($args->ResponseMessages) && isset($args->ResponseMessages->SendNotificationResponseMessage) ){
            if( isset($args->ResponseMessages->SendNotificationResponseMessage->Notification) && isset($args->ResponseMessages->SendNotificationResponseMessage->Notification->SubscriptionId) ){
                $subscriptionId = $args->ResponseMessages->SendNotificationResponseMessage->Notification->SubscriptionId;
                $subscription = ExchangeSubscription::where('subscription_id', '=', $subscriptionId)->first();

                if($subscription)
                    $res = $subscription->handle($args->ResponseMessages->SendNotificationResponseMessage);
            }
        }

        $result = new SendNotificationResultType();
        $result->SubscriptionStatus = $res ? 'OK' : 'Unsubscribe';
        return $result;
    }

    public static function getNotificationsWsdl()
    {
        $ROOT =  dirname(dirname(__FILE__));
        return str_replace('{ROOT}', $ROOT, self::WSDL_NOTIFICATION_SERVICE);
    }

}
