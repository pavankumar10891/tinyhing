@extends('admin.layouts.default')
@section('content')
<section class="content-header">
	<h1>
		{{ trans("Comments") }} 
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li><a href="{{URL::to('admin/blog')}}">{{ trans("Blog Management") }}</a></li>
		<li class="active"> {{ trans("Comments") }}  </li>
	</ol>
</section>
<section class="contect">
	<div class="box">
		<ul class="timeline">
			<li>
				<i class="fa fa fa-clock-o bg-blue"></i>
				<div class="timeline-item">
					<span class="time" style="float:right;">
						<i class="fa fa-clock-o"></i>
							{{ isset($blogDetail['created_at']) ? $blogDetail['created_at']:'' }}
					</span>
					<h3 class="timeline-header" style="padding:10;">
						<a>{{ isset($blogDetail->title) ? $blogDetail->title:'' }}</a>
					</h3>
					<div class="timeline-body" style="padding:10;">
						<p>{{ isset($blogDetail->description) ? $blogDetail->description:'' }}</p>
					</div>
				</div>
			</li>
		</ul>
	</div>
</section>

<section class="content" style="padding-left:55px">
	{{ Form::open(['role' => 'form','url' => 'admin/blog/comment-blog/'.$blogDetail->id,'class' => 'mws-form', 'files' => true]) }}
		{{ Form::hidden('parent_comment_id',0, ['class' => 'form-control']) 	}}
		<div class="form-group <?php echo ($errors->first('comment')?'has-error':''); ?>">
			<div class="mws-form-row">
				{{ HTML::decode( Form::label('comment',trans("Add Comment").'<span class="requireRed"> * </span>', ['class' => 'mws-form-label'])) }}
				<div class="mws-form-item">
				{{ Form::textarea('comment','', ['class' => 'form-control textarea_resize' ,"rows"=>5,"cols"=>5]) 	}}
					<div class="error-message help-inline">
						<?php echo $errors->first('comment'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="mws-button-row">
			<input type="submit" value="{{ trans('SUBMIT') }}" class="btn btn-danger">
		</div>
	{{ Form::close() }}
	<div class="box">
		<ul class="timeline">
			@if(!empty($commentDetails))
			@foreach($commentDetails as $comments)
				<li>
					<i class="fa fa-comments bg-yellow"></i>
					<div class="timeline-item">
						<span class="time" style="float:right;">
							<i class="fa fa-clock-o"></i>
								{{ isset($comments['created_at']) ? $comments['created_at']:'' }}
						</span>
						<h3 class="timeline-header" style="padding:10;">
							<a>{{ isset($comments['name']) ? $comments['name']:'' }}</a>
						</h3>
						<div class="timeline-body" style="padding:10;">
						 <?php $comments['comment'] = str_replace(array("\r\n","\r","\n"),"<br/>", $comments['comment']); ?> 
							<p>{{ isset($comments['comment']) ? $comments['comment']:'' }}</p>
							
						</div>
						<div class="timeline-footer " style="padding:10;">
							<a class="delete_any_item btn btn-danger btn-xs" href="{{ URL::to('admin/blog/delete-comment/'.$comments['id']) }}">
								<i class="fa fa-trash-o"></i>
									&nbsp;Delete Comment
							</a> 
							@if(($comments['user_id'] != Auth::user()->id))
								@if(empty($comments['subcomments']))
									<a class=" btn btn-primary btn-xs commentClass" rel="{{ $comments['id'] }}" href="javascript:void(0)">
									<i class="fa fa-reply"></i>
									&nbsp;Reply
									</a> 
								@endif
							
							@endif
							<a class="btn btn-info btn-xs editComment" rel="{{ $comments['id'] }}" href="javascript:void(0)">
								<i class="fa fa-edit"></i>
									&nbsp;Edit Comment
							</a>
						</div>
						<div style="display:none" id="subComment_{{ $comments['id'] }}">
							{{ Form::open(['role' => 'form','url' => 'admin/blog/reply-comment-blog/'.$blogDetail->id,'class' => 'mws-form', 'files' => true,'id'=>'replycommentfrm_'.$comments['id']]) }}
							{{ Form::hidden('parent_comment_id',$comments['id'], ['class' => 'form-control']) 	}}
							<div class="form-group <?php echo ($errors->first('comment_reply')?'has-error':''); ?>">
								<div class="mws-form-row">
									{{ HTML::decode( Form::label('comment_reply',trans("Add Comment").'<span class="requireRed"> * </span>', ['class' => 'mws-form-label'])) }}
									<div class="mws-form-item">
									{{ Form::textarea('comment_reply','', ['class' => 'form-control textarea_resize' ,"rows"=>5,"cols"=>5,'id'=>'txtreply_'.$comments['id']]) }}
										<div class="error-message help-inline">
											<?php echo $errors->first('comment_reply'); ?>
										</div>
									</div>
								</div>
							</div>
							<div class="mws-button-row">
								<input onclick="reply_comment_function({{ $comments['id'] }})" type="button" value="{{ trans('SUBMIT') }}" class="btn btn-danger">
							</div>
						{{ Form::close() }}
						</div>
						
						
						<div style="padding-left: 185px; display:none" id="change_div<?php echo $comments['id']; ?>">
							{{ Form::open(['role' => 'form','url' => 'admin/blog/edit-comment/'.$comments['id'].'/'.$comments['id'],'class' => 'mws-form', 'files' => true,'id'=>"frmcommentedit_".$comments['id']]) }}
							
							<div class="form-group <?php echo ($errors->first('comment_edit')?'has-error':''); ?>">
								<div class="mws-form-row">
									{{ HTML::decode( Form::label('comment_edit',trans("Edit Comment").'<span class="requireRed"> * </span>', ['class' => 'mws-form-label'])) }}
									<div class="mws-form-item">
									{{ Form::textarea('comment_edit',isset($comments['comment']) ? $comments['comment']:'', ['class' => 'form-control textarea_resize' ,"rows"=>5,"cols"=>5,'id'=>"txtval_".$comments['id']]) 	}}
										<div class="error-message help-inline">
											<?php echo $errors->first('comment_edit'); ?>
										</div>
									</div>
								</div>
							</div>
							<input type="button" onclick="edit_comment_function({{$comments['id']}})" value="{{ trans('SUBMIT') }}" class="btn btn-danger" />
							{{ Form::close() }}
						</div>
						
						<ul class="timeline">
						@if(!empty($comments['subcomments']))
							@foreach($comments['subcomments'] as $subcomment)
								<li>
									<i class="fa fa-comments bg-yellow"></i>
									<div class="timeline-item">
										<span class="time" style="float:right;">
											<i class="fa fa-clock-o"></i>
												{{ isset($subcomment['created_at']) ? $subcomment['created_at']:'' }}
										</span>
										<h3 class="timeline-header" style="padding:10;">
											<a>{{ isset($subcomment['name']) ? $subcomment['name']:'' }}</a>
										</h3>
										<div class="timeline-body" style="padding:10;">
										 <?php $subcomment['comment'] = str_replace(array("\r\n","\r","\n"),"<br/>", $subcomment['comment']); ?> 
											<p>{{ isset($subcomment['comment']) ? $subcomment['comment']:'' }}</p>
											
										</div>
										
										<div class="timeline-footer " style="padding:10;">
											<a class="delete_any_item btn btn-danger btn-xs" href="{{ URL::to('admin/blog/delete-comment/'.$subcomment['id']) }}">
												<i class="fa fa-trash-o"></i>
													&nbsp;Delete Comment
											</a>
											<a class="btn btn-info btn-xs editComment" rel="{{ $subcomment['id'] }}" href="javascript:void(0)">
												<i class="fa fa-edit"></i>
													&nbsp;Edit Comment
											</a>
										</div>
									</div>
									
									<div style="padding-left: 185px; display:none" id="change_div<?php echo $subcomment['id']; ?>">
										{{ Form::open(['role' => 'form','url' => 'admin/blog/edit-comment/'.$comments['id'].'/'.$subcomment['id'],'class' => 'mws-form', 'files' => true, 'id'=>"frmcommentedit_".$subcomment['id']]) }}
										
										<div class="form-group <?php echo ($errors->first('comment_edit')?'has-error':''); ?>">
											<div class="mws-form-row">
												{{ HTML::decode( Form::label('comment_edit',trans("Edit Comment").'<span class="requireRed"> * </span>', ['class' => 'mws-form-label'])) }}
												<div class="mws-form-item">
												{{ Form::textarea('comment_edit',isset($subcomment['comment']) ? $subcomment['comment']:'', ['class' => 'form-control textarea_resize' ,"rows"=>5,"cols"=>5,'id'=>"txtval_".$subcomment['id']]) 	}}
													<div class="error-message help-inline">
														<?php echo $errors->first('comment_edit'); ?>
													</div>
												</div>
											</div>
										</div>
										<input type="button" onclick="edit_comment_function({{$subcomment['id']}})" value="{{ trans('SUBMIT') }}" class="btn btn-danger" />
										{{ Form::close() }}
									</div>
								</li>
							@endforeach
						@endif()
						</ul>
					</div>
				</li>
			@endforeach
			@endif
		</ul>
	</div>
</section>
<script>
	$("input[name=text], textarea").on('click',function(){
		$(this).next().html('');
		$(this).next().removeClass('error');
		$(this).parent().parent().parent().removeClass("has-error");
		//alert(654);
	});
	
	$(".commentClass").on('click',function(){
		var id	=	$(this).attr('rel');
		$("#change_div"+id).hide();
		$("#subComment_"+id).show();
	});
	$(".editComment").on('click',function(){
		var id	=	$(this).attr('rel');
		$("#subComment_"+id).hide();
		$("#change_div"+id).show();
	});
	
	function edit_comment_function(id){
		
		var $inputs = $('#frmcommentedit_'+id+' :input');
		var error  =	0;
		$inputs.each(function() {
			if($(this).val() == '' ){
				$("#txtval_"+id).next().html('.error-message').html(" {{ trans('messages.err.msg.this_field_is_required') }} ");
				$("#txtval_"+id).next().addClass("error");
				$("#txtval_"+id).parent().parent().parent().addClass("has-error");
				error	=	1;
			}
		});
		if(error == 0){
			$("#frmcommentedit_"+id).submit();
		}
	}
	
	function reply_comment_function(id){
		
		var $inputs = $('#replycommentfrm_'+id+' :input');
		var error  =	0;
		$inputs.each(function() {
			if($(this).val() == '' ){
				$("#txtreply_"+id).next().html('.error-message').html(" {{ trans('messages.err.msg.this_field_is_required') }} ");
				$("#txtreply_"+id).next().addClass("error");
				$("#txtreply_"+id).parent().parent().parent().addClass("has-error");
				error	=	1;
			}
		});
		
		if(error == 0){
			$("#replycommentfrm_"+id).submit();
		}
	}
</script>
@stop