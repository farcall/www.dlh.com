<?php echo $this->fetch('header.html'); ?>
    <div class="mb-head">
        <a href="<?php echo url('app=default'); ?>" class="l_b">首页</a>
        <div class="tit">登录</div>
        <a href="<?php echo url('app=member&act=register&ret_url=' . $this->_var['ret_url']. ''); ?>" class="r_b">注册</a>
    </div>
    <script type="text/javascript">
        $(function() {
            $('#login_form').validate({
                errorPlacement: function(error, element) {
                    $(element).parent('td').append(error);
                },
                success: function(label) {
                    label.addClass('validate_right').text('OK!');
                },
                onkeyup: false,
                rules: {
                    user_name: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    captcha: {
                        required: true,
                        remote: {
                            url: 'index.php?app=captcha&act=check_captcha',
                            type: 'get',
                            data: {
                                captcha: function() {
                                    return $('#captcha1').val();
                                }
                            }
                        }
                    }
                },
                messages: {
                    user_name: {
                        required: '您必须提供一个用户名'
                    },
                    password: {
                        required: '您必须提供一个密码'
                    },
                    captcha: {
                        required: '请输入右侧图片中的文字',
                        remote: '验证码错误'
                    }
                }
            });
        });
    </script>

        
        <div class="login_panel">
            <form class="login_box"  method="post" id="login_form">
                <table width="100%">
                    <tr>
                        <td>
                            <input placeholder="用户名" type="text" name="user_name" class="text"/>
                        </td>
                    </tr>
                    <tr> <td><label class="field_message"><span class="field_notice"></span></label></td></tr>
                    <tr><td><input placeholder="密 码" type="password" name="password"  class="text"/> </td> </tr>
                    
                    <?php if ($this->_var['captcha']): ?>
                    <tr>
                        <td>验证码:<input type="text" name="captcha" class="text" id="captcha1" />
                            <a href="javascript:change_captcha($('#captcha'));" class="renewedly"><img id="captcha" src="index.php?app=captcha&amp;<?php echo $this->_var['random_number']; ?>" /></a><label class="field_notice">请输入图片中的文字,点击图片以更换</label></td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr> <td> <input  value="登录" name="Submit"  type="submit" class=" red_btn"/></td></tr>
                    <input type="hidden" name="ret_url" value="<?php echo $this->_var['ret_url']; ?>" />
                </table>
                <p>还不是会员？<a href="<?php echo url('app=member&act=register&ret_url=' . $this->_var['ret_url']. ''); ?>" >立即注册</a></p>
                <p><a href="index.php?app=find_password&act=mobile" style="color: #666;">手机找回密码</a></p>
            </form>
        </div>
        
        
<?php echo $this->fetch('footer.html'); ?>