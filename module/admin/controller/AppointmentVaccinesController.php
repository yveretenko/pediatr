<?php

use App\Entity\AppointmentVaccines;

function vaccinesByWeekAction()
{
    global $em;

    /** @var AppointmentVaccines[] $appointment_vaccines */
    $appointment_vaccines=$em->getRepository(AppointmentVaccines::class)->findFutureAppointmentsVaccines();

    $vaccines_by_week=[];
    foreach ($appointment_vaccines as $appointment_vaccine) {
        $vaccines_by_week[date('W', $appointment_vaccine->getAppointment()->getDate())][$appointment_vaccine->getVaccine()->getShortName()]++;
    }

    die(json_encode($vaccines_by_week));
}