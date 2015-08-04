<div class="box sidebarFilter">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="filterbox">
  <div class="list-group">
    <?php foreach ($filter_groups as $filter_group) { ?>
    <a class="list-group-item" data-id="<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></a>
    <div class="list-group-item">
      <div id="filter-group<?php echo $filter_group['filter_group_id']; ?>" class="filter-group hidden">
        <?php foreach ($filter_group['filter'] as $filter) { ?>
        <?php if (in_array($filter['filter_id'], $filter_category)) { ?>
        <label class="checkbox">
          <input name="filter[]" type="checkbox" value="<?php echo $filter['filter_id']; ?>" checked="checked" />
          <?php echo $filter['name']; ?></label>
        <?php } else { ?>
        <label class="checkbox">
          <input name="filter[]" type="checkbox" value="<?php echo $filter['filter_id']; ?>" />
          <?php echo $filter['name']; ?></label>
        <?php } ?>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
	 <div class="panel-footer text-left">
    <button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
  </div>
  </div>
 
  </div>
</div>
<script type="text/javascript">
    $('.filter-group:first').removeClass('hidden');
    $('a.list-group-item').on('click', function(){
        var group_id = $(this).attr('data-id');
        if (group_id !== undefined && group_id > 0) {
            if ($('#filter-group' + group_id).hasClass('hidden')) {
                $('#filter-group' + group_id).removeClass('hidden');
            } else {
                $('#filter-group' + group_id).addClass('hidden');
            }
        }
    });

$('#button-filter').on('click', function() {
	filter = [];	
	$('input[name^=\'filter\']:checked').each(function(element) {
		filter.push(this.value);
	});
	location = '<?php echo $action; ?>&filter=' + filter.join(',');
});
</script>
