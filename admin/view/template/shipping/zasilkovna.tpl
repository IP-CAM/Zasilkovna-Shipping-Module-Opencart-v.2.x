<?php
$registry = new Registry();
$zasilkovna = new ControllerShippingZasilkovna($registry);
echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-flat" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-flat" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-4 control-label">Název dopravy</label>
						<label class="col-sm-2 control-label">Cena</label>
						<label class="col-sm-2 control-label">Zdarma od</label>
						<label class="col-sm-2 control-label">Cílová země</label>
						<label class="col-sm-2 control-label">Zobrazit</label>
					</div>
					<?php
				for($i=0;$i<$zasilkovna->_servicesCnt;$i++) { ?>
					<div class="form-group">
						<div class="col-sm-4">
							<?php
								$input_field_name = "zasilkovna_title_".$i;
							?> 
							<input class="form-control" type="text" name="<?php echo $input_field_name;?>" value="<?php echo ${$input_field_name};?>" />

						</div>
						<div class="col-sm-2">
							<?php
								$input_field_name = "zasilkovna_price_".$i;
							?> 
							<input class="form-control" type="text" name="<?php echo $input_field_name;?>" value="<?php echo ${$input_field_name};?>" />
						</div>
						<div class="col-sm-2">
							<?php
								$input_field_name = "zasilkovna_freeover_".$i;
							?> 
							<input class="form-control" type="text" name="<?php echo $input_field_name;?>" value="<?php echo ${$input_field_name};?>" />
						</div>
						<div class="col-sm-2">
							<?php $input_field_name = "zasilkovna_destination_".$i;	?>
								<select class="form-control" name="<?php echo $input_field_name;?>">
							<?php
							echo ${$input_field_name};
							foreach ($zasilkovna->countries as $country_code => $country) {
								$selected="";
								if(${$input_field_name}==$country_code){
									$selected=' selected="selected" ';
								}
								?>

								<option value="<?php echo $country_code;?>" <?php echo $selected; ?>><?php echo $country;?></option>
							<?php
							}
							?> 
								</select>
						</div>
						<div class="col-sm-2">
							<?php
								$input_field_name = "zasilkovna_enabled_".$i;
							?>
							<select class="form-control" name="<?php echo $input_field_name;?>">
							<?php if (${$input_field_name}) { ?>
								<option value="1" selected="selected">ano</option>
								<option value="0">ne</option>
								<?php } else { ?>
								<option value="1">ano</option>
								<option value="0" selected="selected">ne</option>
							<?php } ?>
						</select>
						</div>

					</div>
					<?php
				}
				?>
				<div class="form-group">
					<label class="col-sm-2 control-label"><b>Povolit Modul:</b></label>
					<div class="col-sm-3">
				<select class="form-control" name="zasilkovna_status">
							<?php if ($zasilkovna_status) { ?>
							<option value="1" selected="selected">ano</option>
							<option value="0">ne</option>
							<?php } else { ?>
							<option value="1">ano</option>
							<option value="0" selected="selected">ne</option>
							<?php } ?>
						 </select>
			</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><b>API klíč:</b></label>
					<div class="col-sm-3"><?php
								$input_field_name = "zasilkovna_api_key";
							?> 
							<input class="form-control" type="text" size="17" name="<?php echo $input_field_name;?>" value="<?php echo ${$input_field_name};?>" />
							<?php
							if(strlen(${$input_field_name})!=16){
								echo '<p style="color:red; font-weight:bold;">Zadajte správný api klíč! (Získáte na <a href="http://www.zasilkovna.cz/muj-ucet">zasilkovna.cz/muj-ucet</a>)</p>';
							}
							?>
					</div>
				</div>
		<div class="form-group">
					<label class="col-sm-2 control-label">Daň:</label>
					<div class="col-sm-3"><select class="form-control" name="zasilkovna_tax_class_id">
							<option value="0"><?php echo $text_none; ?></option>
							<?php foreach ($tax_classes as $tax_class) { ?>
							<?php if ($tax_class['tax_class_id'] == $zasilkovna_tax_class_id) { ?>
							<option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
							<?php } ?>
							<?php } ?>
						</select></div>
		</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Geo zone</label>
					<div class="col-sm-3"><select class="form-control" name="zasilkovna_geo_zone_id">
							<option value="0"><?php echo $text_all_zones; ?></option>
							<?php foreach ($geo_zones as $geo_zone) { ?>
							<?php if ($geo_zone['geo_zone_id'] == $zasilkovna_geo_zone_id) { ?>
							<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select></div>
		</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Priorita:</label>
					<div class="col-sm-3"><input class="form-control" type="text" name="zasilkovna_sort_order" value="<?php echo $zasilkovna_sort_order; ?>" size="1" /></div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?> 