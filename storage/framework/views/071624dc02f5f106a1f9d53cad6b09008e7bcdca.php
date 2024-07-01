<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div
			class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						Dashboard </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
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
		
			<div class="row">
				<?php /*<div class="col-lg-3">
					<!--begin::Stats Widget 13-->
					<a href="{{ route('Users.index')}}"
						class="card card-custom bg-danger bg-hover-state-danger card-stretch gutter-b">
						<!--begin::Body-->
						<div class="card-body">
							<span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Shopping/Cart3.svg-->
								<svg xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
									height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path
											d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
											fill="#000000" fill-rule="nonzero" opacity="0.3" />
										<path
											d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
											fill="#000000" fill-rule="nonzero" />
									</g>
								</svg>
								<!--end::Svg Icon-->
							</span>
							<!--<span class="symbol symbol-light-success symbol-45 statsCount">
								<span class="symbol-label font-weight-bolder font-size-h6">+17</span>
							</span>-->
							<div class="card-title font-weight-bolder text-light font-size-h2 mb-0 mt-6 d-block">
							{{$totalSubscribers}}</div>
							<div class="font-weight-bold text-light  font-size-sm">Total
								Clients</div>
						</div>
						<!--end::Body-->
					</a>
					<!--end::Stats Widget 13-->
				</div>*/?>
				<div class="col-lg-3">
					<!--begin::Stats Widget 14-->
					<a href="<?php echo e(route('Subscriber.index')); ?>"
						class="card card-custom bg-info bg-hover-state-info card-stretch gutter-b">
						<!--begin::Body-->
						<div class="card-body">
							<span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                   <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg>
								<!--end::Svg Icon-->
							</span>
							<!--<span class="symbol symbol-light-success symbol-45 statsCount">
								<span class="symbol-label font-weight-bolder font-size-h6">+10</span>
							</span>-->
							<div class="card-title font-weight-bolder text-light font-size-h2 mb-0 mt-6 d-block">
							<?php echo e($totalSubscribers); ?></div>
							<div class="font-weight-bold text-light  font-size-sm">Subscribers</div>
							
						</div>
						<!--end::Body-->
					</a>
					<!--end::Stats Widget 14-->
				</div> 
				<div class="col-lg-3">
					<!--begin::Stats Widget 14-->
					<a href="<?php echo e(route('Nanny.index')); ?>"
						class="card card-custom bg-success bg-hover-state-success card-stretch gutter-b">
						<!--begin::Body-->
						<div class="card-body">
							<span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                   <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg>
								<!--end::Svg Icon-->
							</span>
							<!--<span class="symbol symbol-light-success symbol-45 statsCount">
								<span class="symbol-label font-weight-bolder font-size-h6">+10</span>
							</span>-->
							<div class="card-title font-weight-bolder text-light font-size-h2 mb-0 mt-6 d-block">
							<?php echo e($totalNannies); ?></div>
							<div class="font-weight-bold text-light  font-size-sm">Nannies</div>
							
						</div>
						<!--end::Body-->
					</a>
					<!--end::Stats Widget 14-->
				</div>	
			</div>

			<div class="row">
				<div class="col-xl-12">
					<!--begin::Charts Widget 5-->
					<!--begin::Card-->
					<div class="card card-custom gutter-b card-stretch gutter-b">
						<!--begin::Card header-->
						<div class="card-header h-auto border-0">
							<div class="card-title py-5">
								<h3 class="card-label">
									<span class="d-block text-dark font-weight-bolder">Subscribers</span>
									<span class="d-block text-muted mt-2 font-size-sm"></span>
								</h3>
							</div>
							<!--<div class="card-toolbar">
								<ul class="nav nav-pills nav-pills-sm nav-dark-75" role="tablist">
									<li class="nav-item">
										<a class="nav-link py-2 px-4" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_1">
											<span class="nav-text font-size-sm">Month</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link py-2 px-4" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_2">
											<span class="nav-text font-size-sm">Week</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link py-2 px-4 active" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_3">
											<span class="nav-text font-size-sm">Day</span>
										</a>
									</li>
								</ul>
							</div>-->
						</div>
						<!--end:: Card header-->
						<!--begin::Card body-->
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div id="kt_charts_widget_5_chart"></div>
								</div>
							</div>
						</div>
						<!--end:: Card body-->
					</div>
					<!--end:: Card-->
					<!--end:: Charts Widget 5-->
				</div>
			</div>

			<div class="row">
				<div class="col-xl-12">
					<!--begin::Charts Widget 5-->
					<!--begin::Card-->
					<div class="card card-custom gutter-b card-stretch gutter-b">
						<!--begin::Card header-->
						<div class="card-header h-auto border-0">
							<div class="card-title py-5">
								<h3 class="card-label">
									<span class="d-block text-dark font-weight-bolder">Nannies</span>
									<span class="d-block text-muted mt-2 font-size-sm"></span>
								</h3>
							</div>
							<!--<div class="card-toolbar">
								<ul class="nav nav-pills nav-pills-sm nav-dark-75" role="tablist">
									<li class="nav-item">
										<a class="nav-link py-2 px-4" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_1">
											<span class="nav-text font-size-sm">Month</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link py-2 px-4" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_2">
											<span class="nav-text font-size-sm">Week</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link py-2 px-4 active" data-toggle="tab"
											href="#kt_charts_widget_2_chart_tab_3">
											<span class="nav-text font-size-sm">Day</span>
										</a>
									</li>
								</ul>
							</div>-->
						</div>
						<!--end:: Card header-->
						<!--begin::Card body-->
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div id="kt_charts_widget_5_charts"></div>
								</div>
							</div>
						</div>
						<!--end:: Card body-->
					</div>
					<!--end:: Card-->
					<!--end:: Charts Widget 5-->
				</div>
			</div>

		

		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content-->

<script>
	var allUsers = [
		<?php
			if(!empty($allUsers)){
				foreach($allUsers as $allUserss){
					?>
					 [<?php echo $allUserss['month']?>, <?php echo$allUserss['subscribers']; ?>],
					<?php
				}
			}
		?>
		];

		var allNanies = [
		<?php
			if(!empty($allUsers)){
				foreach($allUsers as $allUserss){
					?>
					 [<?php echo $allUserss['month']?>, <?php echo$allUserss['nannies']; ?>],
					<?php
				}
			}
		?>
		];

	$(document).ready(function(){
		var _initChartsWidget5 = function () {
			var element = document.getElementById("kt_charts_widget_5_chart");
			if (!element) {
				return;
			}
			var options = {
				series: [{
					name: 'Total Subscribers',
					type: 'bar',
					//stacked: true,
					data: allUsers
				}],
				chart: {
					stacked: true,
					height: 350,
					toolbar: {
						show: false
					}
				},
				plotOptions: {
					bar: {
						stacked: true,
						horizontal: false,
						endingShape: 'rounded',
						columnWidth: ['12%']
					},
				},
				legend: {
					show: false
				},
				dataLabels: {
					enabled: false
				},
				stroke: {
					curve: 'smooth',
					show: true,
					width: 2,
					colors: ['transparent']
				},
				xaxis: {
					type: 'datetime',
					//categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
					axisBorder: {
						show: false,
					},
					axisTicks: {
						show: false
					},
					labels: {
						style: {
							colors: KTApp.getSettings()['colors']['gray']['gray-500'],
							fontSize: '12px',
							fontFamily: KTApp.getSettings()['font-family']
						},
						/* offsetX: 10,
						formatter: function (value, timestamp) {
							var now = new Date(timestamp);
							day 	= "" + now.getDate(); if (day.length == 1) { day = "0" + day; }
							var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec")
							return day+" "+montharray[now.getMonth()];
						},  */
					}
				},
				yaxis: {
					//max: 120,
					labels: {
						style: {
							colors: KTApp.getSettings()['colors']['gray']['gray-500'],
							fontSize: '12px',
							fontFamily: KTApp.getSettings()['font-family']
						},
						formatter: function(val, index) {
							return Math.round(val);
						  }
					}
				},
				fill: {
					opacity: 1
				},
				states: {
					normal: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					hover: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					active: {
						allowMultipleDataPointsSelection: false,
						filter: {
							type: 'none',
							value: 0
						}
					}
				},
				tooltip: {
					style: {
						fontSize: '12px',
						fontFamily: KTApp.getSettings()['font-family']
					},
					y: {
						formatter: function (val) {
							return val
						}
					}
				},
				colors: [KTApp.getSettings()['colors']['theme']['base']['info'], KTApp.getSettings()['colors']['theme']['base']['primary'], KTApp.getSettings()['colors']['theme']['light']['primary']],
				grid: {
					borderColor: KTApp.getSettings()['colors']['gray']['gray-200'],
					strokeDashArray: 4,
					yaxis: {
						lines: {
							show: true
						}
					},
					padding: {
						top: 0,
						right: 0,
						bottom: 0,
						left: 0
					}
				}
			};
			var chart = new ApexCharts(element, options);
			chart.render();
		}
		_initChartsWidget5();


		var _initChartsWidget5 = function () {
			var element = document.getElementById("kt_charts_widget_5_charts");

			if (!element) {
				return;
			}

			var options = {
				series: [{
					name: 'Total Nannies',
					type: 'bar',
					//stacked: true,
					data: allNanies
				}],
				chart: {
					stacked: true,
					height: 350,
					toolbar: {
						show: false
					}
				},
				plotOptions: {
					bar: {
						stacked: true,
						horizontal: false,
						endingShape: 'rounded',
						columnWidth: ['12%']
					},
				},
				legend: {
					show: false
				},
				dataLabels: {
					enabled: false
				},
				stroke: {
					curve: 'smooth',
					show: true,
					width: 2,
					colors: ['transparent']
				},
				xaxis: {
					type: 'datetime',
					//categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
					axisBorder: {
						show: false,
					},
					axisTicks: {
						show: false
					},
					labels: {
						style: {
							colors: KTApp.getSettings()['colors']['gray']['gray-500'],
							fontSize: '12px',
							fontFamily: KTApp.getSettings()['font-family']
						},
						/* offsetX: 10,
						formatter: function (value, timestamp) {
							var now = new Date(timestamp);
							day 	= "" + now.getDate(); if (day.length == 1) { day = "0" + day; }
							var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec")
							return day+" "+montharray[now.getMonth()];
						},  */
					}
				},
				yaxis: {
					//max: 120,
					labels: {
						style: {
							colors: KTApp.getSettings()['colors']['gray']['gray-500'],
							fontSize: '12px',
							fontFamily: KTApp.getSettings()['font-family']
						},
						formatter: function(val, index) {
							return Math.round(val);
						  }
					}
				},
				fill: {
					opacity: 1
				},
				states: {
					normal: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					hover: {
						filter: {
							type: 'none',
							value: 0
						}
					},
					active: {
						allowMultipleDataPointsSelection: false,
						filter: {
							type: 'none',
							value: 0
						}
					}
				},
				tooltip: {
					style: {
						fontSize: '12px',
						fontFamily: KTApp.getSettings()['font-family']
					},
					y: {
						formatter: function (val) {
							return val
						}
					}
				},
				colors: [KTApp.getSettings()['colors']['theme']['base']['info'], KTApp.getSettings()['colors']['theme']['base']['primary'], KTApp.getSettings()['colors']['theme']['light']['primary']],
				grid: {
					borderColor: KTApp.getSettings()['colors']['gray']['gray-200'],
					strokeDashArray: 4,
					yaxis: {
						lines: {
							show: true
						}
					},
					padding: {
						top: 0,
						right: 0,
						bottom: 0,
						left: 0
					}
				}
			};
			var chart = new ApexCharts(element, options);
			chart.render();
		}
		_initChartsWidget5();
	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/dashboard/dashboard.blade.php ENDPATH**/ ?>