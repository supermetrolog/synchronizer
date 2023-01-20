# **Библиотека для синхронизации файлов**

 Сихронайзер принимает в конструкторе интерфейсы, так что можно использовать репозитории с любой реализацией для работы по различным протоколам и по различным принципам  

# Адаптеры

- Базовый репозиторий (локальная файловая система) [**Source Repository**](https://github.com/supermetrolog/synchronizer-filesystem-source-repository)
- Целевой репозиторий (удаленная файловая система по FTP) [**Target Repository**](https://github.com/supermetrolog/synchronizer-filesystem-ftp-target-repo)
- Репозиторий хранящий информацию о предыдущей синхронизации [**AlreadySync Repository**](https://github.com/supermetrolog/synchronizer-already-sync-repository)

# Builders

- Builder для синхронизации локальных файлов с файлами на удаленном сервере по FTP [**Local To FTP Builder**](https://github.com/supermetrolog/synchronizer-local-to-ftp-builder)