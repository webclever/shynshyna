<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-filter" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php } ?>
    <div class="content">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Загрузка прайса</h3>
        </div>
        <div class="panel-body">
          <form action="<?php echo $action; ?>&load_price" method="post" enctype="multipart/form-data" id="form-available" class="form-horizontal">
              <div class="col-md-12 product-errors" style="display: none; padding: 10px; border: 1px solid lightslategray">
                  <div class="error-caption" style="border-bottom: 1px solid lightslategray">
                      <label>Уведомления:</label>
                  </div>
                  <div class="error-list">
                  </div>
                  <input type="button" class="btn btn-default clear-error" value="Очистить">
              </div>
              <div class="row radio-group">
                  <label class="control-label col-sm-1">Тип прайса:</label>
                  <div class="col-sm-2">
                      <label class="radio-inline">
                          <input type="radio" name="price-type" value="1" checked> В наличии
                      </label>
                      <label class="radio-inline">
                          <input type="radio" name="price-type" value="0"> Под заказ
                      </label>
                  </div>
                  <label  class="col-sm-1 checkbox inline">
                      <input type="checkbox" name="truck-tire" value="1"> Грузовая шина
                  </label>
              </div>
              <div class="row radio-group">
                  <label class="control-label col-sm-1">Сезон: </label>
                  <select class="col-sm-2 selectpicker price-season" name="price-season">
                      <option value="" disabled selected>Виберите сезон</option>
                      <option value="1">Зима</option>
                      <option value="2">Лето</option>
                      <option value="3">Всесезонки</option>
                  </select>
              </div>
              <div class="row radio-group">
                <label class="control-label col-sm-1">Тип обновления остатков:</label>
                <div class="col-sm-2">
                    <label class="radio-inline">
                      <input type="radio" name="price-avails" value="0" checked> Добавить
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="price-avails" value="1"> Заменить
                    </label>
                </div>

              </div>
              <div class="form-group col-md-4">
                <input type="file" class="filestyle" data-buttonText="Вибрать прайс" data-icon="false" name="price_import_file">
                <br />
                <input type="button" class="btn btn-primary check-price" value="Проверить">
                <input type="button" class="btn btn-primary" value="Загрузить" onclick="$('#form-available').submit();">
              </div>

          </form>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Проверить картинки</h3>
        </div>
        <div class="panel-body">
          <form action="<?php echo $action; ?>&reload_image" method="post" enctype="multipart/form-data" id="form-image">
            <h2> </h2>
            <input type="button" class="btn btn-primary" value="ЗРОБИТИ ФАЙНО" onclick="$('#form-image').submit();"><?php if($alert_image) { echo '<pre>'; print_r($alert_image); echo '</pre>'; } ?>
            <?php
              if($alert_image_products_0!=''){
                echo '<p>Вимкнені з продаж товари (немають заголовної картинки):<br /><pre>';
                print_r($alert_image_products_0);
                echo '</pre></p>';
              }
            ?>
            <?php
              if($alert_image_products_1!=''){
                echo '<p>Включені товари:<br /><pre>';
                print_r($alert_image_products_1);
                echo '</pre></p>';
              }
            ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $('.check-price').click(function(){
        var formElement = document.querySelector("form");
        var fd = new FormData(formElement);
        var select = $('.price-season');
        if (select.val() != null) {
            $.ajax({
                url: 'index.php?route=module/price_import/ajaxLoadPrice&token=<?php echo $token; ?>',
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data){
                    data = JSON.parse(data);
                    $('.error-list').html('');
                    if (Array.isArray(data.error)) {
                        var err_length = data.error.length;
                        if (err_length > 0) {
                            $('<p>').text('Total:' + err_length).appendTo('.error-list');
                            for (var i = 0; i < err_length; i++) {
                                $('<p>').text(data.error[i]).appendTo('.error-list');
                            }
                            $('.product-errors').show();
                        } else {
                            $('<p>').text('No errors').appendTo('.error-list');
                            $('.product-errors').show();
                        }
                    } else {
                        $('<p>').text(data.error).appendTo('.error-list');
                        $('.product-errors').show();
                    }
                }
            });
        } else {
            select.children('button').addClass('price-validation-error');
            alert('Виберите сезон!');
        }
    });
    $('.price-season').change(function(){
        if ($(this).val() != null) {
            $('.price-validation-error').removeClass('price-validation-error');
        }
    });
    $('.clear-error').click(function() {
        $('.error-list').html('');
        $('.product-errors').hide();
    });
</script>
<?php echo $footer; ?>