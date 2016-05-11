<footer>
    <div id="footer">
        <div role="navigation">
          <ul class="nav nav-justified nav-orderSystem">
            <?php if($isLogin === true){ ?>
            <li class="<?=$navBarAddOrder ?>" ><a href="<?=Yii::app()->request->baseUrl; ?>/order/add">建立訂單</a></li>
            <li class="<?=$navBarOrder ?>" ><a href="<?=Yii::app()->request->baseUrl; ?>/order/index">訂單管理</a></li>
            <li class="<?=$navBarOrderPrint ?>" ><a href="javascript:void(0);" onclick="if(confirm('確認後開始自動列印')) location.href='<?=Yii::app()->request->baseUrl; ?>/order/printList';">等待列印列表</a></li>
            <li class="<?=$navBarMenu ?>" ><a href="<?=Yii::app()->request->baseUrl; ?>/menu/index">菜單管理</a></li>
            <li class="<?=$navBarUser ?>" ><a href="<?=Yii::app()->request->baseUrl; ?>/user/index">使用者管理</a></li>
            <li ><a href="<?=Yii::app()->request->baseUrl; ?>/user/logout">登出<?=$user->account ?></a></li>
            <?php }else{ ?>
            <li><a href="#">建立訂單</a></li>
            <li><a href="#">訂單管理</a></li>
            <li><a href="#">自動列印列表</a></li>
            <li><a href="#">菜單管理</a></li>
            <li><a href="#">使用者管理</a></li>
            <li class="<?=$navBarUser ?>" ><a href="#">登入</a></li>
            <?php } ?>
          </ul>
        </div>
    </div>
</footer>