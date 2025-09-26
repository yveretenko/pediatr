<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use App\Services\AppointmentService;
use App\Services\BlacklistService;
use App\Services\CalendarService;
use App\Services\FileService;
use App\Services\VaccineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentRepository $appointmentRepository,
        protected BlacklistService $blacklistService,
        protected FileService $fileService,
        protected VaccineService $vaccineService,
        protected CalendarService $calendarService,
        protected AppointmentService $appointmentService
    ) {}

    public function index(Request $request)
    {
        $vaccines=$this->vaccineService->allOrderedByName();
        $query=$request->query();
        $query['comment'] = $request->query('age') ? $request->query('age')."\n\n".$request->query('message') : $request->query('message');
        $query['date'] = $request->query('date') ? date('Y-m-d', strtotime($request->query('date'))) : '';

        $working_days = [1, 2, 3, 4, 5, 6];

        $weeks=$this->calendarService->getWeeks($working_days, 28);

        return view('admin.appointments.index', [
            'vaccines'     => $vaccines,
            'query'        => $query,
            'weeks'        => $weeks,
            'working_days' => $working_days,
        ]);
    }

    public function filter(Request $request): JsonResponse
    {
        return response()->json($this->appointmentService->filter($request->all()));
    }

    public function delete(Appointment $appointment): JsonResponse
    {
        $this->appointmentService->delete($appointment);

        return response()->json(['success' => true]);
    }

    public function file(Appointment $appointment): BinaryFileResponse
    {
        if (!$appointment->file || !$this->fileService->exists($appointment->file))
            abort(404, 'Файл не знайдено');

        return response()->download($this->fileService->getPath($appointment->file), basename($appointment->file));
    }

    public function files()
    {
        $appointments=$this->appointmentService->getAppointmentsWithFiles();

        return view('admin.appointments.files', compact('appointments'));
    }

    public function fileUpload(Appointment $appointment, Request $request): JsonResponse
    {
        $request->validate([
            'file_uploader' => 'required|mimes:doc,docx,pdf',
        ]);

        $this->appointmentService->uploadFile($appointment, $request->file('file_uploader'));

        return response()->json(['error' => null]);
    }

    public function graph()
    {
        return view('admin.appointments.graph');
    }

    public function graphData(): JsonResponse
    {
        return response()->json($this->appointmentService->getGraphData());
    }

    public function history(Request $request)
    {
        $request->validate([
            'tel' => 'required|string',
        ]);

        return response()->json($this->appointmentService->getHistoryByTelephone($request->input('tel')));
    }

    public function getByTelephone(Request $request): JsonResponse
    {
        $request->validate([
            'tel' => 'required|string',
        ]);

        return response()->json($this->appointmentService->getByTelephone($request->input('tel')));
    }

    public function save(AppointmentRequest $request, Appointment $appointment=null): JsonResponse
    {
        $this->appointmentService->save($request, $appointment);

        return response()->json(['success' => true]);
    }
}
