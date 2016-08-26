<?php echo $this->fetch('paycenter/header.html'); ?>

<?php echo $this->fetch('paycenter/left.html'); ?>


<div class="content-wrapper">

    
    <section class="content">
        
        <div class="row">
            
            <section class="col-md-6 connectedSortable">

                <div class="row">
                    <div id="fenpeiliebiao" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">今日分配预览</h3>
                                </div>

                                <div class="box-body  table-responsive no-padding">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 80px">用户ID</th>
                                            <th>用户名</th>
                                            <th>积分赠送权</th>
                                            <th>红积分</th>
                                            <th>现金余额</th>
                                            <th>状态</th>
                                        </tr>
                                        <?php $_from = $this->_var['members']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'member');if (count($_from)):
    foreach ($_from AS $this->_var['member']):
?>
                                        <tr>
                                            <td><?php echo $this->_var['member']['user_id']; ?></td>
                                            <th><?php echo $this->_var['member']['user_name']; ?></th>
                                            <th><?php echo $this->_var['member']['integral_power']; ?></th>
                                            <td>
                                                <?php echo $this->_var['member']['integral_red']; ?>
                                            </td>
                                            <td>
                                                <?php echo $this->_var['member']['money']; ?>
                                            </td>
                                            <td>
                                                <span class="label label-warning">待分配</span>
                                            </td>

                                        </tr>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->fetch('paycenter/page.bottom.html'); ?>
            </section>
            

            <section class="col-md-6 connectedSortable">
                <div class="row info">
                    <div class="col-md-6 col-xs-6">
                        
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?php echo $this->_var['allmoney']; ?>元</h3>

                                <p>今日平台流水</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xs-6">
                        
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo $this->_var['yongjins']; ?>元</h3>

                                <p>今日平台佣金</p>
                            </div>
                        </div>
                    </div>
                    

                    <div class="col-md-6 col-xs-6">
                        
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo $this->_var['integral_power_count']; ?>个</h3>

                                <p>平台积分赠送权之和</p>
                            </div>
                        </div>
                    </div>
                    

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-content">
                                <form method="get">
                                    <div class="box-body">
                                        <input type="hidden" name="app" value="paycenter"/>
                                        <input type="hidden" name="act" value="fanli_ratio"/>

                                        <div class="form-group has-success">
                                            <br/>

                                            <div class="input-group">
                                                <input type="text" class="form-control" id="ratio"
                                                       name="ratio" value="" style="font-size: x-large;">
                                                <label class="input-group-addon">汇率 </label>
                                            </div>
                                        </div>
                                        <button type="submit" name="method" value="submit"
                                                class="btn  btn-warning btn-lg pull-right"> 提交 <span
                                                class="ion-paper-airplane"></span></button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>
        


    </section>
    

</div>



<?php echo $this->fetch('paycenter/footer.html'); ?>
