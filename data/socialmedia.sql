drop database socialmedia;

create database socialmedia;

use socialmedia;

create table login ( codLogin int primary key auto_increment, email varchar (100) not null, senha varchar (300) not null);

create table status ( codStatus int primary key auto_increment, descricaoStatus varchar (300) not null);

create table dadosUsuario ( codUsuario int primary key auto_increment, codLogin int, nome varchar(40) not null, codStatus int);

alter table dadosUsuario add constraint fk_Login foreign key (codLogin) REFERENCES login (codLogin)  ON UPDATE CASCADE ON DELETE CASCADE;	

alter table dadosUsuario add constraint fk_Status foreign key (codStatus) REFERENCES status (codStatus)  ON UPDATE CASCADE ON DELETE CASCADE;	

create table amigo ( codAmigo int primary key auto_increment, codUsuario int, codUsuarioAmigo int);

alter table amigo add constraint fk_Usuario foreign key (codUsuario) REFERENCES dadosUsuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

alter table amigo add constraint fk_UsuarioAmigo foreign key (codUsuarioAmigo) REFERENCES dadosUsuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

create table mensagem ( codMensagem int primary key auto_increment, codUsuario int, descricaoMensagem varchar (300) not null);

alter table mensagem add constraint fk_UsuarioMensagem foreign key (codUsuario) REFERENCES dadosUsuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

-- cadastro de login
insert into login (email, senha) VALUES ('kruzprado@hotmail.com', md5('murilo'));

insert into login (email, senha) VALUES ('guilherme@hotmail.com', md5('guilherme'));

insert into login (email, senha) VALUES ('pedro@hotmail.com', md5('pedro'));

-- cadastro de status
insert into status (descricaoStatus) VALUES ('solteiro');
insert into status (descricaoStatus) VALUES ('em relacionamento serio');
insert into status (descricaoStatus) VALUES ('casado');

-- cadastro de dadosUsuario
insert into dadosUsuario (codLogin, nome, codStatus) VALUES ('1', 'murilo prado', '1');
insert into dadosUsuario (codLogin, nome, codStatus) VALUES ('2', 'guilherme rodrigues', '2');
insert into dadosUsuario (codLogin, nome, codStatus) VALUES ('3', 'pedro', '3');

-- cadastro de amigo
insert into amigo (codUsuario, codUsuarioAmigo) VALUES ('1', '2');
insert into amigo (codUsuario, codUsuarioAmigo) VALUES ('2', '1');
insert into amigo (codUsuario, codUsuarioAmigo) VALUES ('2', '3');
insert into amigo (codUsuario, codUsuarioAmigo) VALUES ('3', '2');


-- cadastro de mensagem
insert into mensagem (codUsuario, descricaoMensagem) VALUES ('1', ' oi oi oi oi');
insert into mensagem (codUsuario, descricaoMensagem) VALUES ('2', ' ola ola ola');
insert into mensagem (codUsuario, descricaoMensagem) VALUES ('2', 'hahahahaha');
insert into mensagem (codUsuario, descricaoMensagem) VALUES ('3', 'mensagem do pedro');
insert into mensagem (codUsuario, descricaoMensagem) VALUES ('3', 'mensagem do pedro 2');


select dadosUsuario.nome nomeUsuario, status.descricaoStatus meuStatus, amigo.codUsuarioAmigo, dadosAmigo.nome nomeAmigo, mensagem.descricaoMensagem mensagemAmigo
from dadosUsuario  
inner join status on (dadosUsuario.codStatus = status.codStatus) 
inner join amigo on (amigo.codUsuario = dadosUsuario.codUsuario) 
inner join mensagem on (amigo.codUsuarioAmigo = mensagem.codUsuario)
inner join dadosUsuario as dadosAmigo on (amigo.codUsuarioAmigo = dadosAmigo.codUsuario) 
where dadosUsuario.codUsuario = '1'; 




