$(function(){var e={};$(".table-edits tr").editable({dropdowns:{gender:["Male","Female"]},edit:function(t){$(".edit i",this).removeClass("fa-pencil-alt").addClass("fa-save").attr("title","Save")},save:function(t){$(".edit i",this).removeClass("fa-save").addClass("fa-pencil-alt").attr("title","Edit"),this in e&&(e[this].destroy(),delete e[this]),updateUser($(this).data('id'))},cancel:function(t){$(".edit i",this).removeClass("fa-save").addClass("fa-pencil-alt").attr("title","Edit"),this in e&&(e[this].destroy(),delete e[this])}})});
function updateUser(id)
{
	var name=$('.table-edits tr[data-id='+id+']').find('td[data-field=name]').text();
	var email=$('.table-edits tr[data-id='+id+']').find('td[data-field=email]').text();
	var designation=$('.table-edits tr[data-id='+id+']').find('td[data-field=designation]').text();
	var mobile=$('.table-edits tr[data-id='+id+']').find('td[data-field=mobile]').text();
	
	$.ajax({
            url:'edit_person',
            type:'get',
            data:{
            	id:id,
            	name:name,
            	email:email,
            	designation:designation,
            	mobile:mobile
            },
            cache: false,
            dataType: 'json',
            success:function(dt)
            {
                console.log(dt);
                if(dt.data==true)
                {
                    $('.alert-success').fadeIn();
                    $('#alerts').html('<div class="alert alert-success alert-dismissable"><strong>'+dt.msg+'</strong></div>');
                    $('.alert-success').fadeOut(3000);
                }
                else
                {
                    $('.alert-danger').fadeIn();
                    $('#alerts').html('<div class="alert alert-danger alert-dismissable"><strong>'+dt.msg+'</strong></div>');
                    $('.alert-danger').fadeOut(3000);
                }
            }
            });
}