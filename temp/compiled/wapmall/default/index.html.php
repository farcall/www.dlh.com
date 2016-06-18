<?php echo $this->fetch('paycenter/header.html'); ?>

<?php echo $this->fetch('paycenter/left.html'); ?>


<div class="content-wrapper">
    <section class="content-header">
        <h1>
            账户预览
            <small>Version 1.0</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">账户预览</li>
        </ol>
    </section>


    <section class="content">
        <h2>现金账户</h2>

        
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="ion ion-social-yen-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">现金余额</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['money']; ?></span>
                        <a href="/index.php?app=epay&act=czlist" target="_blank">
                            <button type="button" class="btn btn-success">充值</button>
                        </a>
                        <a href="/index.php?app=epay&act=withdraw" target="_blank">
                            <button type="button" class="btn btn-danger">提现</button>
                        </a>
                    </div>
                    
                </div>
                
            </div>
            
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon-active"><i class="fa ion-social-chrome-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">冻结资金</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['money_dj']; ?></span>
                    </div>
                    
                </div>
                
            </div>
            

            
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">税费资金</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['money_tax']; ?></span>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        

        <h2>积分账户</h2>

        
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-fuchsia"><i class="ion ion-social-github-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">积分赠送权</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['integral_power']; ?></span>
                    </div>
                    
                </div>
                
            </div>
            
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow-active"><i class="fa ion-social-twitter"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">白积分</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['integral_white']; ?></span>
                    </div>
                    
                </div>
                
            </div>
            

            
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="ion ion-social-reddit-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">红积分</span>
                        <span class="info-box-number"><?php echo $this->_var['member']['integral_red']; ?></span>

                        <form action="index.php?app=integral_red&act=exchange" method="post">
                            <div class="input-group">
                                <input type="number" name="integral_red" placeholder="输入红积分数量" class="form-control">
                                <span class="input-group-btn">
                                     <button type="submit" class="btn btn-success btn-flat">兑换</button>
                                </span>
                            </div>
                        </form>

                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </section>


</div>



<?php echo $this->fetch('paycenter/footer.html'); ?>
