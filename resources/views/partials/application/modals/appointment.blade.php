<form name="sentMessage" id="contactForm" novalidate="novalidate">
    <div class="modal fade" id="appointment_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title font-weight-light text-muted"><i class="fas fa-notes-medical"></i> Запис на прийом</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="name">Ім'я та прізвище дитини</label>
                            <input class="form-control" id="name" type="text" required="required" data-validation-required-message="Будь ласка введіть ім'я та прізвище дитини">
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="phone">Вік дитини</label>
                            <input class="form-control" id="age" type="text">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="phone">Контактний телефон</label>
                            <input class="form-control" id="phone" type="tel" required="required" data-validation-required-message="Будь ласка введіть телефон">
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="message">Коротко опишіть причину звернення</label>
                            <textarea class="form-control" id="message" rows="4" required="required" data-validation-required-message="Будь ласка опишіть причину звернення"></textarea>
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group mb-0">
                            <label class="mb-0" for="date">Оберіть бажану дату</label>
                            <input class="form-control bg-white" type="text" autocomplete="off" readonly id="date" data-date-start-date="<?=date('G')>16 ? date('d.m.Y', strtotime('tomorrow')) : date('d.m.Y') ?>" data-date-end-date="<?=date('d.m.Y', strtotime('last day of next month')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="fa fa-spinner fa-spin text-muted fa-lg d-none"></i>

                    <button type="submit" class="btn btn-success btn-xl" id="sendMessageButton">Відправити</button>
                </div>
            </div>
        </div>
    </div>
</form>
