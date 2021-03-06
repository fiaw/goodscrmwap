{extend name="public/base"}
{block name="header"}
<style>.tab-pane{padding-top:15px;}</style>
{/block}

{block name="main"}
<div class="container-fluid">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet">
 <form class="form-horizontal ajaxForm2" method="post" action="<?php echo url('confirm');?>" id="form1">
  <!-- Tab panes -->
  <div class="tab-content">
  	<input type="hidden" name="id" id="id" value="{$delivery.id}" />
								<input type="hidden" name="cus_id" id="cus_id" value="{$delivery.cus_id}" />
    							<input type="hidden" name="purchase_id" id="purchase_id" value="{$delivery.purchase_id}" />
    							<input type="hidden" name="order_id" id="order_id" value="{$delivery.order_id}" />
								
                    <table class="table contact-template-form" style="margin-bottom: 10px;">
                                <tbody>
                                <tr>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>送货单号:</span></td>
                                    <td width="35%">
                                        {$delivery.order_dn}
                                    </td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>送货日期:</span></td>
                                    <td width="35%">{$delivery.delivery_date}</td>
                                </tr>
                                <tr>
                                <td width="15%" class="right-color"><span class="text-danger">*</span><span>订单号码:</span></td>
                                <td width="35%">
                                	{$delivery.order_sn}
                                	<!-- <button type="button" class="btn btn-primary search_purchase" style="margin-top:-4px;">查找</button> -->
                                </td>
								  <td width="15%" class="right-color"><span class="text-danger">*</span><span>客户订单号:</span></td>
                                  <td width="35%">{$cus_order_sn}</td>
                                </tr>
                             <tr>
                                <td width="15%" class="right-color"><span class="text-danger">*</span><span>送货公司:</span></td>
                                <td width="35%">
                                	{$delivery.cus_name}
                                </td>
								
								<td width="15%" class="right-color"><span class="text-danger">*</span><span>送货地址:</span></td>
                                <td width="35%">
                                	{$delivery.delivery_address}
                                </td>
                                </tr>
                                
                                <tr>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>联系人:</span></td>
                                    <td width="35%">{$delivery.contacts}</td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>电话号码:</span></td>
                                    <td width="35%">
                                        {$delivery.contacts_tel}
                                    </td>
                                </tr>
                                <tr>
                                <td width="15%" class="right-color"><span class="text-danger">*</span><span>关联入库单:</span></td>
                                <td width="35%">
                                	<input type="hidden" name="input_id" id="input_id" value="{$delivery.relation_input_id}" />
                                	{$delivery.input_sn_list}
                                	<button type="button" style="display: none;" class="btn btn-primary relation_order" style="margin-top:-4px;">查找</button>
                                </td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>关联采购单:</span></td>
                                    <td width="35%">
                                        {$delivery.po_sn}
                                    </td>
                                </tr>

                                <tr>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>送货单号:</span></td>
                                    <td width="35%">
                                        {$delivery.delivery_sn}
                                    </td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>交货方式:</span></td>
                                    <td width="35%">{$delivery.delivery_way}</td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>司机:</span></td>
                                    <td width="35%">
                                        {$delivery.delivery_driver}
                                    </td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>司机电话:</span></td>
                                    <td width="35%">{$delivery.driver_tel}</td>
                                </tr>
								<tr>
                                    <td width="15%" class="right-color"><span>备注:</span></td>
                                    <td colspan="3">{$delivery.order_remark}</td>
                                </tr>
                    </tbody>
                    </table>

		<div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table border">
                            <thead>
                                <tr>
                                    <th width="5%">序号</th>
                                    <th width="5%">商品分类</th>
                                    <th width="25%">商品名称</th>
                                    <th width="5%">单位</th>
                                    <th width="10%">未交数量</th>
                                    <!-- <th width="10%">库存数量</th> -->
                                    <th width="10%">本次送货数量</th>
                                    <th width="10%">出库数量</th>
                                    <th width="20%">备注</th>
<!--                                     <th>操作</th> -->
                                </tr>
                            </thead>
                            <tbody class="goodsList"></tbody>
                            
                        </table>

                <!--内容结束-->
            </div>
        </div>


  </div>
                
    <div class="modal-footer" style="border-top:none;">
        <div class="col-md-offset-5 col-md-12 left">
            {if condition="$delivery['is_confirm']"}
            <button type="button" class="btn btn-default" disabled="disabled">已确认</button>
            {if condition="!$delivery['is_print']"}
            <a class="btn btn-primary" href="{:url('prints',['id' => $delivery['id']])}" target="_blank">打印送货单</a>
            {else}
            <a class="btn btn-primary" href="{:url('prints',['id' => $delivery['id']])}" target="_blank">查看文件</a>
            {/if}
            {else}
            <button type="submit" send="confirm" class="btn btn-primary">确 认</button>
            {/if}
            <button type="button" onclick="history.go(-1);" class="btn btn-default">取消</button>
        </div>
    </div>
</form>
                        </div>
                    </div>
                </div>
                <!--内容结束-->
            </div>
{/block}
{block name="footer"}
<!-- Modal -->
<div class="modal fade" id="search_company_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">查找客户</h4>
      </div>
      <div class="modal-body">
		<form class="form-horizontal search_account" method="post" action="<?php echo url('search_account');?>" id="form1">
			
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
<!--         <button type="button" class="btn btn-primary">确认</button> -->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        layui.use('laydate', function() {
            var laydate = layui.laydate;
  		  	laydate.render({
    		    elem: '#delivery_date_'
    		  });
        });
    	
        // 当前页面分类高亮
        $("#sidebar-delivery").addClass("sidebar-nav-active"); // 大分类
        $("#delivery-index").addClass("active"); // 小分类
        
		$('.attrChange').change(function(){
			var goods_type_id = $(this).val();
			$.get('<?php echo url('change_type');?>',{goods_type_id:goods_type_id},function(res){
				$('.appendAttr').html(res);
			});
		});

		$('.search_companyx').click(function(){
			$('#search_company_modal').modal({
				show : true,
				keyboard : false,
				backdrop:'static'
			});
		});
        

        $(".search_purchase").click(function () {
            var title = '查找采购单';
            bDialog.open({
                title : title,
                height: 560,
                width:"90%",
                url : '{:url(\'search_purchase\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        window.location.reload();
                    }
                }
            });
        });

        $(".relation_order").click(function () {
            var title = '查找订单';
            bDialog.open({
                title : title,
                height: 560,
                width:"90%",
                url : '{:url(\'relation_order\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        window.location.reload();
                    }
                }
            });
        });

        $('.get_goods').click(function(){
        	var title = '选择商品';
            bDialog.open({
                title : title,
                height: 560,
                width:960,
                url : '{:url(\'get_goods\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        window.location.reload();
                    }
                }
            });
        });

    });

var goods_info = new Array();
<?php if (!empty($goodslist)){?>
goods_info = <?php echo $goodslist;?>;
goodsList(goods_info);
<?php }?>

var status = 1;
var relation_type = 1; //默认关联订单

function client_info(data){
	relation_type = 1;
	goods_info = [];
	$('#po_sn').val(data.po_sn);
	$('#purchase_date').val(data.purchase_date);
	$('#purchase_money').val(data.total_money);
	if(data.is_cancel == 0){
    	$('#order_sn').val(data.order_sn);
    	$('#order_id').val(data.order_id);
    	$('#purchase_id').val(data.purchase_id);
    	$('#delivery_address').val(data.delivery_address);
    	$('#contacts').val(data.contacts);
    	$('#contacts_tel').val(data.cus_phome);
	}else{
		$('#cus_name').val('');
		$('#order_id').val('');
		$('#purchase_id').val('');
    	$('#order_sn').val('');
    	$('#delivery_address').val('');
    	$('#contacts').val('');
    	$('#contacts_tel').val('');
    	$('#cus_id').val('');
		$('.relation_order').show();
	}
	$.get('<?php echo url('order');?>?purchase_id='+data.purchase_id,{},function(res){
		if(res.code == 1){
			$('#cus_name').val(res.data.cus_name);
			$('#cus_id').val(res.data.cus_id);
			goods_info = res.data.goodslist;
			goodsList(goods_info);
		}else{
			$('#cus_name').val('');
			$('#order_id').val('');
			$('#purchase_id').val('');
	    	$('#order_sn').val('');
	    	$('#delivery_address').val('');
	    	$('#contacts').val('');
	    	$('#contacts_tel').val('');
	    	$('#cus_id').val('');
	    	goodsList(goods_info);
			toastr.error(res.msg);
		}
	});
}

function relation_order(data){
	relation_type = 0;
	goods_info = [];
	$('#purchase_date').val(data.purchase_date);
	$('#order_sn').val(data.order_sn);
	$('#order_id').val(data.orderid);
	$.get('<?php echo url('rel_order');?>?order_id='+data.orderid,{},function(res){
		if(res.code == 1){
			$('#cus_name').val(res.data.cus_name);
			$('#cus_id').val(res.data.cus_id);
	    	$('#order_sn').val(res.data.order_sn);
	    	$('#delivery_address').val(res.data.delivery_address);
	    	$('#contacts').val(res.data.contacts);
	    	$('#contacts_tel').val(res.data.cus_phome);
	    	$('#purchase_money').val(res.data.total_money);
			goods_info = res.data.goodslist;
			goodsList(goods_info);
		}else{
			$('#cus_name').val('');
			$('#order_id').val('');
			$('#purchase_id').val('');
	    	$('#order_sn').val('');
	    	$('#delivery_address').val('');
	    	$('#contacts').val('');
	    	$('#contacts_tel').val('');
	    	$('#cus_id').val('');
	    	goodsList(goods_info);
			toastr.error(res.msg);
		}
	});
}

function goods(data){
	var flag = false;
	for(var i in goods_info){
		if(goods_info[i]['goods_id'] == data.goods_id){
			flag = true;
			break;
		}
	}
	if(!flag){
		goods_info.push(data);
	}
	status = 1;
	goodsList(goods_info);
}

function goodsList(goods_info){
	var html = '';
	for(var j in goods_info){
		var num = parseInt(j)+1;
		html += '<tr data-index="'+j+'" data-goods_id="'+goods_info[j]['goods_id']+'" class="goods_'+j+'">';
		html += '<td>'+num+'</td>';
		html += '<td>'+goods_info[j]['category_name']+'</td>';
		html += '<td style="text-align:left;">'+goods_info[j]['goods_name']+'</td>';
		html += '<td>'+goods_info[j]['unit']+'</td>';
		html += '<td>'+goods_info[j]['diff_number']+'</td>';
		//html += '<td class="store_number"><span class="">'+goods_info[j]['store_number']+'</span></td>';
		html += '<td class="current_send_number"><input type="text" data-current_send_number="'+goods_info[j]['current_send_number']+'" oninput="checkNum2(this)" name="current_send_number" style="width:80px;display:none;" value="'+goods_info[j]['current_send_number']+'" /><span class="inputspan">'+goods_info[j]['current_send_number']+'</span></td>';
		html += '<td class="add_number"><input type="text" data-add_number="'+goods_info[j]['add_number']+'" oninput="checkNum2(this)" name="add_number" style="width:80px;display:none;" value="'+goods_info[j]['add_number']+'" /><span class="inputspan">'+goods_info[j]['add_number']+'</span></td>';
		html += '<td class="remark"><input type="text" name="remark" style="width:200px;display:none;" value="'+goods_info[j]['remark']+'" /><span class="inputspan">'+goods_info[j]['remark']+'</span></td>';
		//html += '<td><a href="javascript:;" onclick="update('+j+')" class="update">修改</a><span class="text-explode">|</span><a href="javascript:;" onclick="_delete('+j+')" class="delete">删除</a></td>';
		html += '</tr>';
	}
	$('.goodsList').html(html);
}

function update(index){
	if(status == 2){
		status = 1;
		var current_send_number = $('.goods_'+index+' input[name=current_send_number]').val();
		if(current_send_number == ''){
			current_send_number = $('.goods_'+index+' input[name=current_send_number]').attr('data-current_send_number');
		}
		var add_number = $('.goods_'+index+' input[name=add_number]').val();
		if(add_number == ''){
			add_number = $('.goods_'+index+' input[name=add_number]').attr('data-add_number');
		}
		
		if(current_send_number > goods_info[index]['store_number']){
			alert('本次送货数量不能大于库存量');
			status = 2;
			return;
		}
		goods_info[index]['current_send_number'] = current_send_number;
		goods_info[index]['add_number'] = add_number;
		goods_info[index]['remark'] = $('.goods_'+index+' input[name=remark]').val();
		goodsList(goods_info);
		return;
	}
	status = 2;
	$('.goods_'+index+' .update').text('保存');
	$('.goods_'+index+' span.inputspan').hide();
	$('.goods_'+index+' input').show();
}

function checkNum(obj){
	obj.value = obj.value.replace(/[^\d.]/g,"");//清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,"");//验证第一个字符是数字而不是
	obj.value = obj.value.replace(/\.{2,}/g,".");//只保留第一个. 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3');//只能输入三个小数.(\d\d\d) 修改个数  加\d
}

function checkNum2(obj){
	obj.value = obj.value.replace(/[^\d]/g,"");//清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,"");//验证第一个字符是数字而不是
	obj.value = obj.value.replace(/\.{1}/g,"");//如果有一个. 就清除
}

function _delete(index){
	status = 1;
	var newGoodsList = new Array();
	for(var i in goods_info){
		if(i != index){
			newGoodsList.push(goods_info[i]);
		}
	}
	goods_info = newGoodsList;
	goodsList(goods_info);
}
$('button[type=submit]').click(function(){
	var send = $(this).attr('send');
	$('.ajaxForm2').ajaxSubmit({
		data:{goods_info:goods_info,relation_type:relation_type,type:send},
		success: function(res){
			if(res.code == 1){
				toastr.success(res.msg);
				if(res.url != '' && typeof res.url != 'undefined'){
					setTimeout(function(){window.location.href = res.url;},2000);
					}else{
						setTimeout(function(){window.location.reload();},2000);
						}
			}else{
				toastr.error(res.msg);
				}
		}
	});
	return false;});
</script>
{/block}