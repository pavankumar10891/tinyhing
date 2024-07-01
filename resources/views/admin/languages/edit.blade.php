
{{ Form::open(['role' => 'form','url' => 'adminpnlx/language-settings/edit-setting/'.$Id,'class' => 'mws-form','id'=>'LanguageSettingEditForm','onsubmit'=>'return false;']) }}
		
			<div class="mws-form-inline">
				<div class="mws-form-row">
					<div class="mws-form-item">
					<div class="controls" style="margin-left:0px;margin-bottom:0px;">
						{{ Form::text('word', stripslashes($result->msgstr), ['class' => 'small','style'=>"height:30px;width:200px;font-size:9pt;",'id'=>'edit_msgstr']) }}
						<?php echo $errors->first('word'); ?><br/>
						<input type="submit" value="{{ trans('Save') }}" id="editgroup" class="btn btn-primary">
						<a id="cancel" class="btn btn-primary" href="javascript:void(0);">{{ trans('Reset') }}</a>
					</div>	
					</div>
				</div>
			</div>
{{ Form::close() }} 	

