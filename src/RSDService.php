<?php

namespace Mojoblanco\ScheduleDelivery;


use Mojoblanco\ScheduleDelivery\Helpers\ApiHelper;
use Mojoblanco\ScheduleDelivery\Requests\PAYERequest;
use Mojoblanco\ScheduleDelivery\Requests\PensionRequest;
use Mojoblanco\ScheduleDelivery\Requests\ScheduleDeliveryRequest;
use Mojoblanco\ScheduleDelivery\Requests\VATRequest;
use Mojoblanco\ScheduleDelivery\Requests\WHTRequest;

class RSDService
{
    /**
     * @var string
     */
    private $url;

    /**
     * RSDService constructor.
     */
    public function __construct($env)
    {
        if ($env == 'PROD') {
            $this->url = 'https://login.remita.net/remita/exapp/api/v1/send/api/schedulesvc/schedule-delivery/request';
        } else {
            $this->url = 'https://remitademo.net/remita/exapp/api/v1/send/api/schedulesvc/schedule-delivery/request';
        }
    }


    /**
     * @param string $scheduleType
     * @param string $reference
     * @param array $details
     * @return mixed
     */
    public function sendSchedule(string $scheduleType, string $reference, array $details) {
        $this->ValidateSchedule($scheduleType, $details);

        $request = $this->getScheduleRequest($reference, $scheduleType, $details);

        return ApiHelper::makeRequest($this->url, $request);
    }


    /**
     * @return string
     */
    private function getRequestId()
    {
        return (string) round(microtime(true) * 1000);
    }

    /**
     * @param string $reference
     * @param string $scheduleType
     * @param array $details
     * @return ScheduleDeliveryRequest
     */
    private function getScheduleRequest(string $reference, string $scheduleType, array $details)
    {
        $request = new ScheduleDeliveryRequest();
        $request->requestId = $this->getRequestId();
        $request->scheduleType = $scheduleType;
        $request->paymentReference = $reference;
        $request->totalRecords = '0';
        $request->details = $details;
        return $request;
    }

    /**
     * @param string $scheduleType
     * @param array $details
     */
    private function ValidateSchedule(string $scheduleType, array $details)
    {
        if ($details[0] instanceof PAYERequest && $scheduleType != 'TAX')
            throw new \InvalidArgumentException('Wrong schedule type');

        if ($details[0] instanceof WHTRequest && $scheduleType != 'WHT')
            throw new \InvalidArgumentException('Wrong schedule type');

        if ($details[0] instanceof PensionRequest && $scheduleType != 'PENSION')
            throw new \InvalidArgumentException('Wrong schedule type');

        if ($details[0] instanceof VATRequest && $scheduleType != 'VAT')
            throw new \InvalidArgumentException('Wrong schedule type');
    }
}