<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Models\Appointment;

class AppointmentRepository
{
    protected Appointment $model;

    public function __construct(Appointment $model)
    {
        $this->model=$model;
    }

    public function findByFilters(array $filters, $order_field=null, $order_where=null, $start=null, $limit=null)
    {
        $query=$this->model->query();

        if (!empty($filters['start_timestamp']))
            $query->where('date', '>', $filters['start_timestamp']);

        if (!empty($filters['end_timestamp']))
            $query->where('date', '<', $filters['end_timestamp']);

        if (!empty($filters['name']))
            $query->where('name', 'like', '%'.$filters['name'].'%');

        if (!empty($filters['comment']))
            $query->where('comment', 'like', '%'.$filters['comment'].'%');

        if (!empty($filters['vaccine']))
        {
            if ($filters['vaccine']==='any')
                $query->whereHas('vaccines');
            else
                $query->whereHas('vaccines', function ($q) use ($filters) {
                    $q->where('vaccines.id', $filters['vaccine']);
                });
        }

        if (!empty($filters['min_visits']))
            $query->whereNotNull('tel')
                ->whereIn('tel', function($q) use ($filters) {
                    $q->select('tel')
                        ->from('appointments')
                        ->whereNotNull('tel')
                        ->groupBy('tel')
                        ->havingRaw('COUNT(*) >= ?', [$filters['min_visits']]);
                });

        if (!empty($filters['tel']))
        {
            $tel=$filters['tel'];

            if (str_starts_with($tel, '*') || str_ends_with($tel, '*'))
            {
                if (str_starts_with($tel, '*'))
                    $tel='%'.substr($tel, 1);

                if (str_ends_with($tel, '*'))
                    $tel=substr($tel, 0, -1).'%';
            }
            else
                $tel='%'.StringHelper::normalizeTelephone($tel).'%';

            $query->where('tel', 'like', $tel);
        }


        if (!is_null($order_field) && !is_null($order_where))
            $query->orderBy($order_field, $order_where);

        if (!is_null($start) && !is_null($limit))
        {
            $query->skip($start);
            $query->take($limit);
        }

        return $query->get();
    }

    public function findPastByTelephone(string $tel)
    {
        return Appointment
            ::where('tel', $tel)
            ->where('date', '<', time())
            ->orderByDesc('date')
            ->get()
        ;
    }

    public function getLastNameByTelephone(string $tel)
    {
        return Appointment
            ::where('tel', $tel)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderByDesc('date')
            ->value('name')
        ;
    }

    public function getGraphData()
    {
        return Appointment
            ::where('date', '>', strtotime('2021-01-04'))
            ->orderBy('date', 'asc')
            ->get()
            ;
    }

    public function getNextAppointment(int $timestamp)
    {
        return Appointment
            ::where('date', '>', $timestamp)
            ->where('date', '<', strtotime('tomorrow', $timestamp))
            ->orderBy('date', 'asc')
            ->first()
            ;
    }

    public function getAppointmentsWithFiles()
    {
        return Appointment
            ::whereNotNull('file')
            ->orderBy('file', 'asc')
            ->get()
        ;
    }
}
