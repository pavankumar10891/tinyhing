<?php if($results->isNotEmpty()): ?> 
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row py-3">
                        <div class="col-auto">
                            <div class="Post-date">
                                Posted on: <?php echo e(date('d/m/Y',strtotime($result->created_at))); ?>

                            </div>
                            <h3><?php echo e($result->name); ?></h3>
                            
                            <?php
                                    $currentDate=date('m-d-Y');
                                    $dataDate=date('m-d-Y',strtotime($result->created_at));
                                    $currentHour=date('H'); 
                                    $dataHour=date('H',strtotime($result->created_at)); 
                                    $currentMinute=date('i'); 
                                    $dataMinute=date('i',strtotime($result->created_at)); 
                            ?>
                            <div class="Post-date">
                                
                                <?php
                                if($currentDate == $dataDate){
                                    if($currentHour - $dataHour >=1){
                                       echo $currentHour - $dataHour." Hrs ago"; 
                                    }else{
                                        echo $currentMinute - $dataMinute." mins ago";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="review-block pl-md-3">

                                <span>
                                    <?php for($i = 0; $i < 5; $i++): ?>
                                        <?php if($i >= $result->rating): ?>
                                            <i class="far fa-star"></i>
                                        <?php else: ?>
                                            <i class="fas fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>

                                <div class="text-block ">
                                    <p>
                                      <?php echo e($result->review); ?> </p>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="btn-block py-3 "  >
                       
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/ratings_data.blade.php ENDPATH**/ ?>