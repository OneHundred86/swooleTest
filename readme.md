1、先安装php swoole插件
https://github.com/swoole/swoole-src

2、启动websocket服务
php artisan swoole:websocket_server

3、配置nginx
    location /ws/ {
        proxy_pass http://localhost:9502;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header  X-Real-IP $remote_addr;
        proxy_set_header  X-Forwarded-Proto https;
        proxy_set_header  X-Forwarded-For $proxy_add_x_forwarded_for;
    }

4、然后访问 /chat 可以进入聊天室

5、todo