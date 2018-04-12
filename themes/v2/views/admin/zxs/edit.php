<?php
$this->pageTitle = $this->controllerName.'新建/编辑';
$this->breadcrumbs = array($this->controllerName.'管理', $this->pageTitle);
$parentArea = AreaExt::model()->parent()->normal()->findAll();
$parent = $article->area?$article->area:(isset($parentArea[0])?$parentArea[0]->id:0);
$childArea = $parent ? AreaExt::model()->getByParent($parent)->normal()->findAll() : array(0=>'--无子分类--');
?>
<?php $this->widget('ext.ueditor.UeditorWidget',array('id'=>'UserExt_content','options'=>"toolbars:[['fullscreen','source','undo','redo','|','customstyle','paragraph','fontfamily','fontsize'],
        ['bold','italic','underline','fontborder','strikethrough','superscript','subscript','removeformat',
        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain','|',
        'forecolor','backcolor','insertorderedlist','insertunorderedlist','|',
        'rowspacingtop','rowspacingbottom', 'lineheight','|',
        'directionalityltr','directionalityrtl','indent','|'],
        ['justifyleft','justifycenter','justifyright','justifyjustify','|','link','unlink','|',
        'insertimage','emotion','scrawl','insertvideo','music','attachment','map',
        'insertcode','|',
        'horizontal','inserttable','|',
        'print','preview','searchreplace']]")); ?>
<?php $form = $this->beginWidget('HouseForm', array('htmlOptions' => array('class' => 'form-horizontal'))) ?>
<div class="form-group">
    <label class="col-md-2 control-label">名字<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'name', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'name') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">手机号<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'phone', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'phone') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">身份证<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'id_card', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'id_card') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">机构<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'company', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'company') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">学历</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'edu',  Yii::app()->params['edu'], array('class'=>'form-control select2','empty'=>'无')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'edu') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">资质</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'mid', Yii::app()->params['zz'], array('class'=>'form-control select2','empty'=>'无')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'mid') ?></div>
</div>
<div class="form-group">
                <label class="col-md-2 control-label text-nowrap">所在区域<span class="required" aria-required="true">*</span></label>
                <div class="col-md-10">
                    <?php
                    echo $form->dropDownList($article , 'area' ,CHtml::listData($parentArea,'id','name') , array(
                            'class'=>'form-control input-inline',
                            'ajax' =>array(
                                'url' => Yii::app()->createUrl('admin/area/ajaxGetArea'),
                                'update' => '#PlotExt_street',
                                'data'=>array('area'=>'js:this.value'),
                            )
                        )
                    );
                    ?>
                    <?php
                    echo $form->dropDownList($article , 'street' ,$childArea ? CHtml::listData($childArea,'id','name'):array(0=>'--无子分类--') , array('class'=>'form-control input-inline'));
                  ?>
                    <span class="help-block"><?php echo $form->error($article, 'area').$form->error($article, 'street'); ?></span>
                </div>
            </div>
            <div class="form-group">
    <label class="col-md-2 control-label">工作开始年份<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'work_year', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'work_year') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">是否支持线下资讯</label>
    <div class="col-md-4">
        <?php echo $form->radioButtonList($article, 'zx_mode', ['支持','不支持'], array('separator' => '')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'zx_mode') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">线下资讯场所<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'place', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'place') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">收费<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'price', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'price') ?></div><div class="help-block">每小时收费</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">收费介绍<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'price_note', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'price_note') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">微信<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'wx', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'wx') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">专长</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'zc',  CHtml::listData(TagExt::model()->findAll("cate='zc'"),'id','name'), array('class'=>'form-control select2','empty'=>'无')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'zc') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">领域</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'ly',  CHtml::listData(TagExt::model()->findAll("cate='ly'"),'id','name'), array('class'=>'form-control select2','empty'=>'无')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'ly') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">个人简介</label>
    <div class="col-md-8">
        <?php echo $form->textArea($article, 'content', array('id'=>'UserExt_content')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'content')  ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label text-nowrap">头像</label>
    <div class="col-md-8">
        <?php $this->widget('FileUpload',array('model'=>$article,'attribute'=>'image','inputName'=>'img','width'=>400,'height'=>300)); ?>
        <span class="help-block">建议尺寸：430*230</span> 
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label text-nowrap">身份证正面</label>
    <div class="col-md-8">
        <?php $this->widget('FileUpload',array('model'=>$article,'attribute'=>'id_pic_main','inputName'=>'img','width'=>400,'height'=>300)); ?>
        <!-- <span class="help-block">建议尺寸：430*230</span>  -->
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label text-nowrap">身份证反面</label>
    <div class="col-md-8">
        <?php $this->widget('FileUpload',array('model'=>$article,'attribute'=>'id_pic_sec','inputName'=>'img','width'=>400,'height'=>300)); ?>
        <!-- <span class="help-block">建议尺寸：430*230</span>  -->
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">性别</label>
    <div class="col-md-4">
        <?php echo $form->radioButtonList($article, 'sex', UserExt::$sex, array('separator' => '')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'sex') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">状态</label>
    <div class="col-md-4">
        <?php echo $form->radioButtonList($article, 'zxs_status', UserExt::$status, array('separator' => '')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'zxs_status') ?></div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <button type="submit" class="btn green">保存</button>
            <?php echo CHtml::link('返回',$this->createUrl('list'), array('class' => 'btn default')) ?>
        </div>
    </div>
</div>

<?php $this->endWidget() ?>

<?php
$js = "

    var getHousesAjax =
     {
        url: '".$this->createUrl('/admin/plot/AjaxGetHouse')."',"."
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                kw:params
            };
        },
        results:function(data){
            var items = [];

             $.each(data.results,function(){
                var tmp = {
                    id : this.id,
                    text : this.name
                }
                items.push(tmp);
            });

            return {
                results: items
            };
        },
        processResults: function (data, page) {
            var items = [];
             $.each(data.msg,function(){
                var tmp = {
                    id : this.id,
                    text : this.title
                }
                items.push(tmp);
            });
            return {
                results: items
            };
        }
    }
        $(function(){

           $('.select2').select2({
              placeholder: '请选择',
              allowClear: true
           });

				var houses_edit = $('#plot');
				var data = {};
				if( houses_edit.length && houses_edit.data('houses') ){
					data = eval(houses_edit.data('houses'));
				}

				$('#plot').select2({
					multiple:true,
					ajax: getHousesAjax,
					language: 'zh-CN',
					initSelection: function(element, callback){
						callback(data);
					}
				});

             $('.form_datetime').datetimepicker({
                 autoclose: true,
                 isRTL: Metronic.isRTL(),
                 format: 'yyyy-mm-dd hh:ii',
                 // minView: 'm',
                 language: 'zh-CN',
                 pickerPosition: (Metronic.isRTL() ? 'bottom-right' : 'bottom-left'),
             });

             $('.form_datetime1').datetimepicker({
                 autoclose: true,
                 isRTL: Metronic.isRTL(),
                 format: 'yyyy-mm-dd',
                 minView: 'month',
                 language: 'zh-CN',
                 pickerPosition: (Metronic.isRTL() ? 'bottom-right' : 'bottom-left'),
             });
        });
        ";


Yii::app()->clientScript->registerScript('add',$js,CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2_locale_zh-CN.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
Yii::app()->clientScript->registerCssFile('/static/admin/pages/css/select2_custom.css');

Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/addCustomizeDialog.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/static/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js', CClientScript::POS_END, array('charset'=> 'utf-8'));
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootbox/bootbox.min.js', CClientScript::POS_END);
?>
