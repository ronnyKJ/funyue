define(['bootstrap'], function(_bs){

  return function cover() {

    $('.update').click(function(ev){
      var img = $(this).parent().find('input').val();
      $.ajax({
          type:"POST",
          url:"/admin/cover/update",
          data:{
            id:$(this).attr('data-id'),
            img:img
          },
          success:function(json){
            alert('更新成功');
            window.location.reload();
          }
        });
    });



    $('.add').click(function(){
        var date = $('#today-date').val();
        if($('.d_' + date).length > 0 || !$('.today-img').val()){
          alert('封面日期不能重复 或 封面路径不能为空');
          return;
        }

        var img = $('.today-img').val();
        $.ajax({
            type:"POST",
            url:"/admin/cover/add",
            data:{
              img:img,
              date:date
            },
            success:function(json){
              alert('添加成功');
              window.location.reload();
            }
          });
      });


  };



});