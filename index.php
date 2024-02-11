<?php
require_once(__DIR__.'/connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ai-gallery</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/master.css">
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100;300;500;700&family=Noto+Sans:wght@100;300;500;700&family=Roboto:wght@100;300;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="loading"></div>

    <div class="mainWrap">
        <div class="creatSideBox">
            <div class="inner">
                <input type="text" placeholder="Enter a noun and AI will draw a picture for you...">
                <div class="btn generate">Generate!</div>
            </div>
        </div>
        <div class="galleryBox">
        </div>
    </div>
</body>
<script>

var checkGenerateStatusTimer;
var AweAjaxLoading = new function(){
    this.deep = 0;
    this.start = function(){
        $('body').addClass('ajax-loading');
        this.deep++;
    }

    this.end = function(){
        this.deep--;
        if(this.deep <= 0){
            $('body').removeClass('ajax-loading');
            this.deep = 0;
        }
    }
};


$(function(){

    //取得列表
    getList();

    //按下產生
    $('.creatSideBox .generate').on('click', function(){

        let noun = $('.creatSideBox input[type="text"]').val();

        if(!noun){
            return;
        }

        AweAjaxLoading.start();

        $.ajax({
            url: 'generate.php',
            dataType: 'json',
            method: 'post',
            data: {
                noun: noun
            },
            success: function(result){
                AweAjaxLoading.end();
                console.log(result);

                let id = result.id || 0;

                if(id != 0){

                    callAi();
                    
                    AweAjaxLoading.start();
                    checkGenerateStatus(id);
                }

            },
            error: function(){
                AweAjaxLoading.end();
            }
        });
        

    });

});

function getList()
{
    $.ajax({
        url: 'list.php',
        dataType: 'json',
        method: 'get',
        data: {
        },
        success: function(result){
            console.log(result);

            result = result.reverse();

            result.forEach(element => {

                //檢查是否存在，存在則跳過
                if($('.galleryBox .gallery[data-id="'+element.id+'"]').length > 0){
                    return;
                }

                renderGallery(element);
            });
        },
        error: function(){
        }
    });
}

function renderGallery(data)
{
    let galleryTemplate = `
        <div class="gallery" data-id="${data.id}">
            <div class="inner">
                <iframe class="previewIframe" src="preview.php?id=${data.id}" frameborder="0" loading="lazy"></iframe>
            </div>
            <div class="infoBox">
                <div class="name">${data.name}</div>
                <div class="author">create by ${data.author}</div>
                <div class="date">${data.date}</div>
            </div>
        </div>
    `;
    $('.galleryBox').prepend(galleryTemplate);
}

function callAi()
{
    $.ajax({
        url: 'callAi.php',
        method: 'get',
        data: {
        },
        success: function(result){
        },
        error: function(){
        }
    });
}

function checkGenerateStatus(id)
{
    clearTimeout(checkGenerateStatusTimer);

    AweAjaxLoading.start();

    $.ajax({
        url: 'generateSataus.php',
        dataType: 'json',
        method: 'post',
        data: {
            id: id
        },
        success: function(result){
            AweAjaxLoading.end();
            console.log(result);

            if(result.status == 2){
                // window.location.reload();
                AweAjaxLoading.end();
                getList();
            }
            else{
                checkGenerateStatusTimer = setTimeout(function(id){
                    checkGenerateStatus(id);
                }, 5000, id);
            }

        },
        error: function(){
            AweAjaxLoading.end();
        }
    });
}

</script>
</html>