<?php

namespace Controllers;

use DateTime;
use Model\Positions;
use Model\Reports;
use Model\Rooms;
use Model\Staff;
use Services\Validator;

class BaseController{
    /**
     * Подключает шаблон страницы
     *
     * @return array
     * @throws \Exception
     */
    public function view() {
        return template('base', [
            'staff' => $this->getStaff(),
            'reports' => $this->getReports(),
            'rooms' => Rooms::all()->get(),
            'positions' => Positions::all()->get(),
        ]);
    }

    /**
     * Возвращает список отчетов
     *
     * @return array
     * @throws \Exception
     */
    private function getReports() {
        $reports = [];
        foreach (Reports::all()->getCollection() as $obReport) {
            $person = $obReport->person()->get();
            $room = $obReport->room()->get();
            $arReport = $obReport->get();

            $reports[] = [
                'key' => $arReport['key'],
                'name_person' => $person['full_name'],
                'room_number' => $room['number'],
                'date_of_issue' => (new DateTime($arReport['date_of_issue']))->format('d.m.Y H:i:s')
            ];
        }

        return $reports;
    }

    /**
     * Возвращает список сотрудников
     *
     * @return array
     */
    private function getStaff() {
        $staff = [];

        foreach (Staff::all()->getCollection() as $obPerson) {
            $arPerson = $obPerson->get();
            $position = $obPerson->position()->get();

            $staff[] = [
                'id' => $arPerson['id'],
                'full_name' => $arPerson['full_name'],
                'position' => $position['name'],
            ];
        }

        return $staff;
    }

    /**
     * Выдача ключа сотруднику
     *
     * @param $request
     * @return array|bool[]
     */
    public function issueKey($request) {
        $validation = Validator::make($request, [
            'staff_id' => 'required|numeric',
            'number_room' => 'required|numeric',
        ]);

        $fails = $validation->fails();
        if($fails){
            return ['success' => false, 'message' => $fails];
        }

        $obPerson = Staff::find($request['staff_id']);
        $rooms = $obPerson->position()->roomAccess()->get();
        $roomIds = [];
        foreach ($rooms as $room) {
            $roomIds[] = $room['id'];
        }
        $room = Rooms::where('number', $request['number_room'])->get();

        $report = Reports::where('room_id', $room['id'])->get();
        if (!empty($report) && $report['staff_id'] == $request['staff_id']) {
            $person = $obPerson->get();
            return ['success' => false, 'message' => 'Для ' . $person['full_name'] . ' уже выдан ключ в комнату №' . $request['number_room'] . '!'];
        }

        $result = false;
        if(in_array($room['id'], $roomIds)) {
            $report = [
                'key' => uniqid(),
                'room_id' => $room['id'],
                'staff_id' => $request['staff_id'],
                'date_of_issue' => date('Y-m-d H:i:s')
            ];
            $result = Reports::add($report);
        }

        if ($result) {
            $roomsResult = Rooms::update($room['id'], ['alarm_status' => false]);
            return ($roomsResult) ? ['success' => true] : ['success' => false, 'message' => 'Не удалось отключить сигнализацию в комнате №' . $request['number_room']];
        }

        return ($result) ? ['success' => true] : ['success' => false, 'message' => 'Не удалось выдать ключ, возможно нет доступа'];
    }

    /**
     * Добавляет сотрудника
     *
     * @param $request
     * @return array|bool[]
     */
    public function addStaff($request) {
        $validation = Validator::make($request, [
            'full_name' => 'required|only_letters_ru',
            'position_id' => 'required|numeric',
        ]);

        $fails = $validation->fails();
        if($fails){
            return ['success' => false, 'message' => $fails];
        }

        $result = Staff::add($request);

        return ($result) ? ['success' => true] : ['success' => false, 'message' => 'Не удалось добавить сотрудника'];
    }

    /**
     * Удаляет сотрудника
     *
     * @param $request
     * @return array|bool[]
     */
    public function deleteStaff($request) {
        $validation = Validator::make($request, [
            'id' => 'required|numeric',
        ]);

        $fails = $validation->fails();
        if($fails){
            return ['success' => false, 'message' => $fails];
        }
        $result = Staff::delete($request['id']);

        return ($result) ? ['success' => true] : ['success' => false, 'message' => 'Не удалось удалить сотрудника'];
    }

    /**
     * Возвращает данные сотрудника по id
     *
     * @param $request
     * @return array
     */
    public function getDataStaff($request) {
        $validation = Validator::make($request, [
            'id' => 'required|numeric',
        ]);

        $fails = $validation->fails();
        if($fails){
            return ['success' => false, 'message' => $fails];
        }

        return ['success' => true, 'data' => Staff::find($request['id'])->get()];
    }

    /**
     * Обновляет данные сотрудника
     *
     * @param $request
     * @return array|bool[]
     */
    public function updateStaff($request) {
        $validation = Validator::make($request, [
            'id' => 'required|numeric',
            'full_name' => 'required|only_letters_ru',
            'position_id' => 'required|numeric',
        ]);

        $fails = $validation->fails();
        if($fails){
            return ['success' => false, 'message' => $fails];
        }
        $id = $request['id'];
        unset($request['id']);

        $result = Staff::update($id, $request);

        return ($result) ? ['success' => true] : ['success' => false, 'message' => 'Данные сотрудника не обновлены'];
    }
}