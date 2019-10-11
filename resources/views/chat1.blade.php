<!DOCTYPE html>
<html>
<head>
    <title>简易聊天Demo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: "Microsoft Yahei", sans-serif, Arial;
        }

        .container {
            text-align: center;
        }

        .title {
            font-size: 16px;
            color: rgba(0, 0, 0, 0.3);
            position: fixed;
            line-height: 30px;
            height: 30px;
            left: 0px;
            right: 0px;
            background-color: white;
        }

        .content {
            background-color: #f1f1f1;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            margin-top: 30px;
        }

        .content .show-area {
            text-align: left;
            padding-top: 8px;
            padding-bottom: 168px;
        }

        .content .show-area .message {
            width: 70%;
            padding: 5px;
            word-wrap: break-word;
            word-break: normal;
        }

        .content .write-area {
            position: fixed;
            bottom: 0px;
            right: 0px;
            left: 0px;
            z-index: 10;
            width: 100%;
            height: 200px;
            border-top: 1px solid #d8d8d8;
        }

        .content .write-area .send {
            border-radius: 4px;
            margin-top: 10px;
        }

        .content .write-area #name {
            position: relative;
            height: 28px;
            line-height: 28px;
            font-size: 13px;
            outline: none;
            border: 1px solid #eee;
            margin-right: 20px;
            padding: 0 10px;
        }

        .content .btn-join {
            box-sizing: border-box;
            height: 28px;
            line-height: 28px;
            border-radius: 4px;
        }
        .content .tip {
            position: absolute;
            top: -30px;
            width: 100%;
            height: 30px;
            line-height: 30px;
            text-align: center;
            color: red;
            display: none;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="title">简易聊天demo</div>
    <div class="content">
        <div class="show-area"></div>
        <div class="write-area">
            <div id="tip" class="tip"></div>
            <div style="margin: 10px 0;">
                <input name="name" id="name" type="text" placeholder="input your name">
                <button id="join" class="btn-join">加入</button>
            </div>
            <div>
                <textarea name="message" id="message" cols="38" rows="4" placeholder="input your message..." style="display:none"></textarea>
            </div>
            <div>
                <button class="btn btn-default send" style="display:none" id="send">发送</button>
            </div>
        </div>
    </div>
</div>

<script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script>
    function enterChat(){
        $('#tip').hide();
        $('#message').show();
        $('#send').show();
        $('#name').attr('disabled', true).css('backgroundColor', '#f4f4f4');
        $('#join').hide();
    };

    $(function () {
        // var wsurl = 'ws://localhost:9502';
        var wsurl = 'ws://' + window.location.host + '/ws/';
        var websocket;
        var i = 0;


        if (window.WebSocket) {
            websocket = new WebSocket(wsurl);

            //连接建立
            websocket.onopen = function (evevt) {
                console.log("Connected to WebSocket server.");
                $('.show-area').append('<p class="bg-info message"><i class="glyphicon glyphicon-info-sign"></i>Connected to WebSocket server!</p>');
            }
            //收到消息
            websocket.onmessage = function (event) {
                var msg = JSON.parse(event.data); //解析收到的json消息数据

                if(msg.errcode !== 0) {
                    $('#tip').html(msg.errmessage).show();
                    return;
                }

                // 加入聊天室成功
                if(msg.c == 'user' && msg.a == 'join'){
                    enterChat();
                    return;
                }

                if(msg.c == 'user' && msg.a == 'msg'){
                    var umsg = msg.data.msg; //消息文本
                    var uname = msg.data.from_user_name; //发送人
                    i++;

                    $('.show-area').append('<p class="bg-success message"><i class="glyphicon glyphicon-user"></i><a name="' + i + '"></a><span class="label label-primary">' + uname + ' : </span>' + umsg + '</p>');
                
                    $('#message').val('');
                    window.location.hash = '#' + i;
                }
            }

            //发生错误
            websocket.onerror = function (event) {
                i++;
                console.log("Connected to WebSocket server error");
                $('.show-area').append('<p class="bg-danger message"><a name="' + i + '"></a><i class="glyphicon glyphicon-info-sign"></i>Connect to WebSocket server error.</p>');
                window.location.hash = '#' + i;
            }

            //连接关闭
            websocket.onclose = function (event) {
                i++;
                console.log('websocket Connection Closed. ');
                $('.show-area').append('<p class="bg-warning message"><a name="' + i + '"></a><i class="glyphicon glyphicon-info-sign"></i>websocket Connection Closed.</p>');
                window.location.hash = '#' + i;
            }

            function send() {
                var message = $('#message').val();
                if (!message) {
                    alert('发送消息不能为空!');
                    return false;
                }
                var msg = {
                    c: 'user',
                    a: 'msg',
                    msg: message,
                };
                try {
                    websocket.send(JSON.stringify(msg));
                } catch (ex) {
                    console.log(ex);
                }
            }

            //按下enter键发送消息
            $(window).keydown(function (event) {
                if (event.keyCode == 13) {
                    console.log('user enter');
                    send();
                }
            });

            //点发送按钮发送消息
            $('.send').bind('click', function () {
                send();
            });

            // 定时发送心跳包
            setInterval(function () {
                console.log('interval');

                if(websocket.readyState!=1){
                    console.log('websocket未连接');
                    return;
                }

                var msg = {
                    c: 'system',
                    a: 'heartbeat',
                };
                websocket.send(JSON.stringify(msg));
            }, 10000);


            $('#join').bind('click', function () {
                var msg = {
                        c: 'user',
                        a: 'join',
                        name: $('#name').val()
                    };
                websocket.send(JSON.stringify(msg));
            });

        }
        else {
            alert('该浏览器不支持web socket');
        }

    });
</script>
</body>
</html>