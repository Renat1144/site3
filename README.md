# Turkey Signature

Премиальный одностраничный WordPress-сайт об авторских путешествиях по Турции. Контент, шапка и подвал собраны из блоков Gutenberg и редактируются в Site Editor.

## Локальный запуск

Требуется установленный и запущенный Docker Desktop.

```sh
./local-setup.sh
```

После первого запуска скрипт автоматически создаст локальные пароли, установит WordPress и активирует тему.

- Сайт: http://localhost:8082/
- Админка: http://localhost:8082/wp-admin/
- Локальные данные для входа: `.local-admin.txt`

Файлы `.env`, `.local-admin.txt`, `wp-config.php`, база данных, загрузки WordPress и архивы переноса не публикуются в Git.

Подробности: [LOCAL_SETUP.md](LOCAL_SETUP.md).
