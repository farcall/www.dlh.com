<?php echo $this->fetch('footer_order_notice.html'); ?>
<div id="footer" class="w-full">

    <!--<div id="service-2014" class="clearfix">-->
    <!--<div class="slogen">-->
    
    <!--</div>-->

    <!--</div>-->


    <div id="footer-2014" class="w">
        <div class="links">
            <a target="_blank" href="/index.php?app=article&act=view&articl- 0--e_id=9">关于我们</a>|
            <a target="_blank" href="/index.php?app=article&act=view&article_id=12">联系我们</a>|
        </div>
        <div class="copyright">
            © <?php echo $this->_var['icp_number']; ?><br>
            Copyright&nbsp;©&nbsp;2004-2015&nbsp;&nbsp;www.delaihui.com&nbsp;版权所有<br>合作平台：<a
                target="_blank" href="https://pay.weixin.qq.com/index.php">微信支付</a>&nbsp;&nbsp;<a
                href="http://www.alipay.com/"
                target="_blank">支付宝</a>&nbsp;&nbsp;<a
                href="https://www.wangyin.com/" target="_blank">网银在线</a>

        </div>

        <div class="authentication">

            <a href="http://www.sdca.gov.cn/index.html">
                <img width="103" height="32" alt="经营性网站备案中心"
                     src="http://img12.360buyimg.com/da/jfs/t535/349/1185317137/2350/7fc5b9e4/54b8871eNa9a7067e.png"
                     class="err-product">
            </a>

            <a href="http://www.kxnet.cn/">
                <img border="true" width="103" height="32" onclick="CNNIC_change('urlknet')"
                     oncontextmenu="return false;" name="CNNIC_seal" alt="可信网站"
                     src="http://img11.360buyimg.com/da/jfs/t643/61/1174624553/2576/4037eb5f/54b8872dNe37a9860.png"
                     class="err-product">
            </a>

            <a href="http://www.bj.cyberpolice.cn/index.do">
                <img width="103" height="32" alt="朝阳网络警察"
                     src="http://img11.360buyimg.com/da/jfs/t559/186/1172042286/2795/7d90b036/54b8874bN694454a5.png"
                     class="err-product">
            </a>


            <a target="_blank" href="http://www.szfw.org/">
                <img width="103" height="32"
                     src="http://img11.360buyimg.com/da/jfs/t451/173/1189513923/1992/ec69b14a/54b8875fNad1e0c4c.png"
                     class="err-product">
            </a>
            <a href="http://jubao.china.cn:13225/reportform.do">
                <img width="185" height="32"
                     src="http://img10.360buyimg.com/da/jfs/t520/303/1151687373/1180/2f8340fc/54b8863dN8d2c61ec.png"
                     class="err-product">
            </a>

        </div>
        <?php echo $this->_var['statistics_code']; ?>
    </div>


    <?php echo $this->_var['async_sendmail']; ?>


    <div class="mui-mbar-tabs clearfix">
        <div class="mui-mbar-tabs-mask ">
            <div class="mui-mbar-tab mui-mbar-tab-cart" style="top: 150px;">
                <div class="mui-mbar-tab-logo mui-mbar-tab-logo-cart"></div>
                <div class="mui-mbar-tab-txt"><a href="<?php echo url('app=cart'); ?>">购物车</a></div>
                <div class="mui-mbar-tab-sup">
                    <div class="mui-mbar-tab-sup-bg">
                        <div class="mui-mbar-tab-sup-bd"><?php echo $this->_var['cart_goods_kinds']; ?></div>
                    </div>
                </div>
            </div>


            <div class="mui-mbar-tab mui-mbar-tab-top" style="bottom: 200px;" id="gotop">
                <div class="mui-mbar-tab-logo mui-mbar-tab-logo-top"></div>
                <div class="mui-mbar-tab-tip" style="right: 35px;  display: none;">
                    <a href="javascript:scrool(0,0)">返回顶部</a>

                    <div class="mui-mbar-arr mui-mbar-tab-tip-arr">◆</div>
                </div>
            </div>
            <div class="mui-mbar-tab mui-mbar-tab-qrcode" style="bottom: 250px;">
                <div class="mui-mbar-tab-logo mui-mbar-tab-logo-qrcode"></div>
                <div class="mui-mbar-tab-tip mui-mbarp-qrcode-tip" style="right: 35px;  display: none;">
                    <div class="mui-mbarp-qrcode-hd">
                        <img src="<?php echo $this->_var['default_qrcode']; ?>" width="140" height="140">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var screen_height = window.screen.height;
            $(".mui-mbar-tabs-mask").css("height", screen_height);
            $('.mui-mbar-tab').hover(function () {
                $(this).addClass("mui-mbar-tab-hover");
                $(this).find('.mui-mbar-tab-tip').fadeIn(500);
            }, function () {
                $(this).removeClass("mui-mbar-tab-hover");
                $(this).find('.mui-mbar-tab-tip').fadeOut(500);
            });
        });

    </script>

</div>
</body>
</html>