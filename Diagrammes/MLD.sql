create table doctrine_migration_versions
(
    version        varchar(191) not null
        primary key,
    executed_at    datetime     null,
    execution_time int          null
)
    collate = utf8mb3_unicode_ci;

create table groupe
(
    id         int auto_increment
        primary key,
    name       varchar(100) not null,
    created_at date         not null comment '(DC2Type:date_immutable)'
)
    collate = utf8mb4_unicode_ci;

create table messenger_messages
(
    id           bigint auto_increment
        primary key,
    body         longtext     not null,
    headers      longtext     not null,
    queue_name   varchar(190) not null,
    created_at   datetime     not null comment '(DC2Type:datetime_immutable)',
    available_at datetime     not null comment '(DC2Type:datetime_immutable)',
    delivered_at datetime     null comment '(DC2Type:datetime_immutable)'
)
    collate = utf8mb4_unicode_ci;

create index IDX_75EA56E016BA31DB
    on messenger_messages (delivered_at);

create index IDX_75EA56E0E3BD61CE
    on messenger_messages (available_at);

create index IDX_75EA56E0FB7336F0
    on messenger_messages (queue_name);

create table user
(
    id              int auto_increment
        primary key,
    name            varchar(50)  not null,
    email           varchar(255) not null,
    password        varchar(255) not null,
    created_at      date         not null comment '(DC2Type:date_immutable)',
    is_verified     tinyint(1)   not null,
    token_validator varchar(255) null,
    roles           json         not null
)
    collate = utf8mb4_unicode_ci;

create table figure
(
    id          int auto_increment
        primary key,
    user_id     int          not null,
    groupe_id   int          not null,
    name        varchar(100) not null,
    description longtext     not null,
    created_at  date         not null comment '(DC2Type:date_immutable)',
    slug        varchar(255) not null,
    updated_at  varchar(50)  null,
    constraint FK_2F57B37A7A45358C
        foreign key (groupe_id) references groupe (id),
    constraint FK_2F57B37AA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_2F57B37A7A45358C
    on figure (groupe_id);

create index IDX_2F57B37AA76ED395
    on figure (user_id);

create table image
(
    id         int auto_increment
        primary key,
    figure_id  int          not null,
    user_id    int          not null,
    path       varchar(255) not null,
    created_at date         not null comment '(DC2Type:date_immutable)',
    banner     tinyint(1)   not null,
    constraint FK_C53D045F5C011B5
        foreign key (figure_id) references figure (id),
    constraint FK_C53D045FA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_C53D045F5C011B5
    on image (figure_id);

create index IDX_C53D045FA76ED395
    on image (user_id);

create table message
(
    id         int auto_increment
        primary key,
    figure_id  int      not null,
    user_id    int      not null,
    content    longtext not null,
    created_at date     not null comment '(DC2Type:date_immutable)',
    constraint FK_B6BD307F5C011B5
        foreign key (figure_id) references figure (id),
    constraint FK_B6BD307FA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_B6BD307F5C011B5
    on message (figure_id);

create index IDX_B6BD307FA76ED395
    on message (user_id);

create table video
(
    id         int auto_increment
        primary key,
    figure_id  int         not null,
    user_id    int         not null,
    path       varchar(50) not null,
    created_at date        not null comment '(DC2Type:date_immutable)',
    constraint FK_7CC7DA2C5C011B5
        foreign key (figure_id) references figure (id),
    constraint FK_7CC7DA2CA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_7CC7DA2C5C011B5
    on video (figure_id);

create index IDX_7CC7DA2CA76ED395
    on video (user_id);


