$(function () {
    /**
     * Отправляет запрос на выдачу ключа
     *
     * @param e
     * @returns {Promise<void>}
     */
    extradite_form.onsubmit = async (e) => {
        e.preventDefault();
        await fetch('/issue-key', {
            method: 'POST',
            body: new FormData(extradite_form)
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(data.success){
                window.location.reload();
            } else {
                setErrors(data.message);
            }
        });
    };

    /**
     * Отправляет запрос на добавление сотрудника
     *
     * @param e
     * @returns {Promise<void>}
     */
    staff_form.onsubmit = async (e) => {
        e.preventDefault();
        await fetch('/add-staff', {
            method: 'POST',
            body: new FormData(staff_form)
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(data.success){
                window.location.reload();
            } else {
                setErrors(data.message);
            }
        });
    };

    /**
     * Отправляет запрос на удаление сотрудника
     *
     * @param e
     * @returns {Promise<void>}
     */
    delete_staff_form.onsubmit = async (e) => {
        e.preventDefault();
        await fetch('/delete-staff', {
            method: 'POST',
            body: new FormData(delete_staff_form)
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(data.success){
                window.location.reload();
            } else {
                setErrors(data.message);
            }
        });
    };

    /**
     * Отправляет запрос на обновление данных сотрудника
     *
     * @param e
     * @returns {Promise<void>}
     */
    update_staff_form.onsubmit = async (e) => {
        e.preventDefault();
        await fetch('/update-staff', {
            method: 'POST',
            body: new FormData(update_staff_form)
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(data.success){
                window.location.reload();
            } else {
                let errors = {};
                if(data.message.constructor === Object) {
                    for (let id in data.message) {
                        errors['update_form_' + id] = data.message[id];
                    }
                } else {
                    errors = data.message;
                }
                setErrors(errors);
            }
        });
    };

    /**
     * Отправляет запрос на получение данных сотрудника по id
     *
     * @returns {Promise<void>}
     */
    update_form_id.onchange = async () => {
        var params = new URLSearchParams();
        params.set('id', update_form_id.value);
        await fetch('/get-staff', {
            method: 'POST',
            body: params
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if(response.success){
                for(let id in response.data){
                    let field = $('#update_form_' + id);
                    field.val(response.data[id]);
                    field.closest('.form-group').removeClass('d-none');
                }
            } else {
                setErrors(response.message);
            }
        });
    };

    /**
     * Выводит ошибки
     *
     * @param data
     */
    function setErrors(data) {
        cleanErrors();
        if(data.constructor === Object){
            for(let id in data){
                let field = $('#' + id);
                let formGroup = field.closest('.form-group');
                let errorBlock = formGroup.find('.error');

                field.css('border', '2px solid red');
                formGroup.find('label').css('color', 'red');
                if (errorBlock.length === 0) {
                    formGroup.append('<p class="error">' + data[id] + '</p>');
                } else {
                    errorBlock.text(data[id]);
                }
            }
        } else {
            alert(data);
        }
    }

    /**
     * Очищает вывод ошибок
     */
    function cleanErrors() {
        let formGroup = $('.form-group');
        formGroup.find('.error').remove();
        formGroup.find('label').css('color', '#fff');
        $('.form-control').css('border', 'none');
    }

    /**
     * Отправляет запрос на выгрузку данных сотрудника
     *
     * @param e
     * @returns {Promise<void>}
     */
    discharge_form.onsubmit = async (e) => {
        e.preventDefault();
        let url = e.submitter.dataset.url;
        await fetch(url, {
            method: 'POST',
            body: new FormData(discharge_form)
        }).then((response) => {
            return response.blob()
        }).then((blob) => {
            if(blob.type === 'application/json') {
                let reader = new FileReader();
                reader.readAsText(blob)
                reader.onload = function () {
                    let errors = {};
                    let data = JSON.parse(reader.result);
                    if(data.message.constructor === Object) {
                        for (let id in data.message) {
                            errors['discharge_' + id] = data.message[id];
                        }
                    } else {
                        errors = data.message;
                    }
                    setErrors(errors);
                };
            } else {
                let file = window.URL.createObjectURL(blob);
                window.open(file);
                cleanErrors();
            }
        });
    };
});