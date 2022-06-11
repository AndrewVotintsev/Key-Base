<?/** @var $staff - список персонала*/?>
<?/** @var $rooms - список кабинетов*/?>
<?/** @var $reports - список выданных ключей*/?>
<?/** @var $positions - список должностей*/?>

<?$APPLICATION->setTitle('База ключей');?>
<?$APPLICATION->addCss('/template/css/base.css');?>
<?$APPLICATION->addJs('/template/js/base.js');?>
<div class="base">
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-2">
                <h3>Выдать ключ</h3>
                <form action="/" method="get" class="p-2" id="extradite_form">
                    <div class="form-group">
                        <label for="staff_id">Имя сотрудника</label>
                        <select class="form-control" name="staff_id" id="staff_id">
                            <option value="" disabled selected>Выберите сотрудника</option>
                            <?foreach($staff as $person){?>
                                <option value="<?=$person['id']?>"><?=$person['full_name']?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="number_room">Номер кабинета</label>
                        <input type="text" class="form-control" id="number_room" name="number_room" placeholder="Введите номер кабинета" value="">
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Выдать</button>
                </form>
                <h3 class="mt-5">Добавить сотрудника</h3>
                <form action="/" method="get" class="p-2" id="staff_form">
                    <div class="form-group">
                        <label for="full_name">Имя сотрудника</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Введите имя сотрудника" value="">
                    </div>
                    <div class="form-group">
                        <label for="position_id">Должность</label>
                        <select class="form-control" name="position_id" id="position_id">
                            <option value="" disabled selected>Выберите должность</option>
                            <?foreach($positions as $position){?>
                                <option value="<?=$position['id']?>"><?=$position['name']?></option>
                            <?}?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Добавить</button>
                </form>
                <h3 class="mt-5">Удалить сотрудника</h3>
                <form action="/" method="get" class="p-2" id="delete_staff_form">
                    <div class="form-group">
                        <select class="form-control" name="id" id="id">
                            <option value="" disabled selected>Выберите сотрудника</option>
                            <?foreach($staff as $person){?>
                                <option value="<?=$person['id']?>"><?=$person['full_name']?></option>
                            <?}?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Удалить</button>
                </form>
                <h3 class="mt-5">Изменить данные сотрудника</h3>
                <form action="/" method="get" class="p-2" id="update_staff_form">
                    <div class="form-group">
                        <select class="form-control" name="id" id="update_form_id">
                            <option value="" disabled selected>Выберите сотрудника</option>
                            <?foreach($staff as $person){?>
                                <option value="<?=$person['id']?>"><?=$person['full_name']?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group d-none">
                        <label for="update_full_name">Имя сотрудника</label>
                        <input type="text" class="form-control" id="update_form_full_name" name="full_name" placeholder="Введите имя сотрудника" value="">
                    </div>
                    <div class="form-group d-none">
                        <label for="update_position_id">Должность</label>
                        <select class="form-control" name="position_id" id="update_form_position_id">
                            <option value="" disabled selected>Выберите должность</option>
                            <?foreach($positions as $position){?>
                                <option value="<?=$position['id']?>"><?=$position['name']?></option>
                            <?}?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Изменить</button>
                </form>
            </div>
            <div class="col-3">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Название</th>
                        <th scope="col">Номер</th>
                        <th scope="col">Сигнализация</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?foreach($rooms as $room){?>
                        <tr>
                            <td><?=$room['name']?></td>
                            <td><?=$room['number']?></td>
                            <td><?=($room['alarm_status']) ? 'Вкл' : 'Выкл'?></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
            <div class="col-2">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">ФИО</th>
                        <th scope="col">Должность</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?foreach($staff as $person){?>
                        <tr>
                            <td><?=$person['full_name']?></td>
                            <td><?=$person['position']?></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
            <div class="col-5">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Ключ</th>
                        <th scope="col">Сотрудник</th>
                        <th scope="col">Номер кабинета</th>
                        <th scope="col">Дата выдачи</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?foreach($reports as $report){?>
                        <tr>
                            <td><?=$report['key']?></td>
                            <td><?=$report['name_person']?></td>
                            <td><?=$report['room_number']?></td>
                            <td><?=$report['date_of_issue']?></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
