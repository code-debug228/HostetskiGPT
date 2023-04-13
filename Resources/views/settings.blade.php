@extends('layouts.app')

@section('title', __('Calendar'))
@section('content_class', 'content-full')

@section('content')
    {{ $name  }}
    <div style="font-size: 24px; text-align: center; margin-top: 5px;">HostetskiGPT</div>
    <form class="form-horizontal margin-top margin-bottom" method="POST" action="">
    {{ csrf_field() }}

    <div class="form-group">
        <label class="col-sm-2 control-label">GPT токен</label>

        <div class="col-sm-6">
            <input name="token" class="form-control" placeholder="sk-..." />
        </div>
    </div>

    <div class="form-group margin-top">
        <label class="col-sm-2 control-label">Вывод</label>

        <div class="col-sm-6">
            <select style="width: 100%;background-color: white;border: 1px solid #c1cbd4; border-radius: 3px; padding: 4px 6px 4px 8px" id="stat-memory">
                <option>Примечание</option>
                <option>Сообщение</option>
            </select>
        </div>
    </div>

    <div class="form-group margin-top margin-bottom">
        <div class="col-sm-6 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">
                Сохранить
            </button>
        </div>
    </div>
</form>
@endsection

@section('body_bottom')
    @parent
    
@endsection
