
<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					<?php echo e($sectionName); ?> </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
						</li>
					</ul>
					<!--end::Breadcrumb-->
				</div>
				<!--end::Page Heading-->
			</div>
			<!--end::Info-->

			<?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
	</div>
	<!--end::Subheader-->

	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class=" container ">
			<?php echo e(Form::open(['method' => 'get','role' => 'form','route' => "$modelName.index",'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"])); ?>

			<?php echo e(Form::hidden('display')); ?>

			<div class="row">
				<div class="col-12">
					<div class="card card-custom card-stretch card-shadowless">
						<div class="card-header">
							<div class="card-title">
							</div>
							<div class="card-toolbar">
								<a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2"
								data-toggle="collapse" data-target="#collapseOne6">
								Search
							</a>
							<a href='<?php echo e(route("$modelName.add")); ?>' class="btn btn-primary">
							<?php echo e(trans("Add New ")); ?><?php echo e($sectionNameSingular); ?> </a>
						</div>
					</div>
					<div class="card-body">
						<div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
							<div id="collapseOne6"
							class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>"
							data-parent="#accordionExample6">
							<div>
								<div class="row mb-6">
									<div class="col-lg-4 mb-lg-5 mb-6">
										<label>City</label>
										<?php echo e(Form::text('city',((isset($searchVariable['city'])) ? $searchVariable['city'] : ''), ['class' => ' form-control','placeholder'=>'City'])); ?>

									</div>
								</div>

								<div class="row mt-8">
									<div class="col-lg-12">
										<button class="btn btn-primary btn-primary--icon" id="kt_search">
											<span>
												<i class="la la-search"></i>
												<span>Search</span>
											</span>
										</button>
										&nbsp;&nbsp;

										<a href='<?php echo e(route("$modelName.index")); ?>'
										class="btn btn-secondary btn-secondary--icon">
										<span>
											<i class="la la-close"></i>
											<span>Clear Search</span>
										</span>
									</a>
								</div>
							</div>
							<!--begin: Datatable-->
							<hr>
						</div>
					</div>
				</div>
				<div class="dataTables_wrapper ">
					<div class="table-responsive">
						<table
						class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
						id="taskTable">
						<thead>
							<tr class="text-uppercase">
								
								<th
								class="<?php echo e((($sortBy == 'city' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'city' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
								<?php echo e(link_to_route(
									"$modelName.index",
									trans("City"),
									array(
									'sortBy' => 'city',
									'order' => ($sortBy == 'city' && $order == 'desc') ? 'asc' : 'desc',
									$query_string
									)
									)); ?>

							</th>
							<th
							class="<?php echo e((($sortBy == 'tax' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'tax' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
							<?php echo e(link_to_route(
								"$modelName.index",
								trans("Tax(%)"),
								array(
								'sortBy' => 'tax',
								'order' => ($sortBy == 'tax' && $order == 'desc') ? 'asc' : 'desc',
								$query_string
								)
								)); ?>

						</th>

						<th class="text-right">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if(!$results->isEmpty()): ?>
					<?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td>
							<div class="text-dark-75 mb-1 font-size-lg">
								<?php echo e($result->city); ?>

							</div>
						</td>
						<td>
							<div class="text-dark-75 mb-1 font-size-lg">
								<?php echo e($result->tax); ?>

							</div>
						</td>
						
						<td class="text-right pr-2">
							
							
							<a href='<?php echo e(route("$modelName.edit","$result->id")); ?>'
								class="btn btn-icon btn-light btn-hover-primary btn-sm"
								data-toggle="tooltip" data-placement="top" data-container="body"
								data-boundary="window" title="" data-original-title="Edit">
								<span class="svg-icon svg-icon-md svg-icon-primary">
									<svg xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
									height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none"
									fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24" />
									<path
									d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
									fill="#000000" opacity="0.3" />
									<path
									d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
									fill="#000000" />
								</g>
							</svg>
						</span>
					</a>

					<a href='<?php echo e(route("$modelName.delete","$result->id")); ?>'
						class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete"
						data-toggle="tooltip" data-placement="top" data-container="body"
						data-boundary="window" title="" data-original-title="Delete">
						<span class="svg-icon svg-icon-md svg-icon-danger">
							<svg xmlns="http://www.w3.org/2000/svg"
							xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
							height="24px" viewBox="0 0 24 24" version="1.1">
							<g stroke="none" stroke-width="1" fill="none"
							fill-rule="evenodd">
							<rect x="0" y="0" width="24" height="24" />
							<path
							d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
							fill="#000000" fill-rule="nonzero" />
							<path
							d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
							fill="#000000" opacity="0.3" />
						</g>
					</svg>
				</span>
			</a>
		</td>
	</tr>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<?php else: ?>
	<tr><td colspan="6" style="text-align:center;"><?php echo e(trans("Record not found.")); ?></td></tr>
	<?php endif; ?>
</tbody>
</table>
</div>
<?php echo $__env->make('pagination.default', ['results' => $results], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
</div>
</div>
</div>
</div>
<?php echo e(Form::close()); ?>

</div>
<!--end::Container-->
</div>
<!--end::Entry-->
</div>
<!--end::Content-->

<script>
	$(document).ready(function() {
		

		$(".confirmDelete").click(function(e) {
			e.stopImmediatePropagation();
			url = $(this).attr('href');
			Swal.fire({
				title: "Are you sure?",
				text: "Want to delete this ?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "Yes, delete it",
				cancelButtonText: "No, cancel",
				reverseButtons: true
			}).then(function(result) {
				if (result.value) {
					window.location.replace(url);
				} else if (result.dismiss === "cancel") {
					Swal.fire(
						"Cancelled",
						"Your imaginary file is safe :)",
						"error"
						)
				}
			});
			e.preventDefault();
		});

	});

	$('.chosenselect').select2({
		placeholder: "Select Country",
		allowClear: true
	});

	function page_limit() {
		$("form").submit();
	}
</script>

<style>
.label.label-inline.label-lg {
	padding: 1.1rem 0.75rem;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/tax/index.blade.php ENDPATH**/ ?>