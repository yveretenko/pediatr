<div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title font-weight-light text-muted"><i class="fa fa-notes-medical"></i> Послуги</h3>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h5>Консультація</h5>

                <ul class="list-group my-3">
                    @foreach($services as $name => $price)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $name }}</span>
                            <span class="text-nowrap pl-3">{{ $price }} грн</span>
                        </li>
                    @endforeach
                </ul>

                <h5>Вакцинація</h5>

                <p class="small text-muted">
                    У вартість послуги входить огляд лікаря, послуга вакцинації.
                    <br>
                    Уточнюйте наявність вакцин перед записом.
                </p>

                <ul class="list-group mt-3">
                    @foreach($vaccines as $vaccine)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $vaccine->name }}</span>
                            <span class="text-nowrap">{{ $vaccine->price }} грн</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
