drop database socialmedia;

create database socialmedia;

use socialmedia;

create table status ( codStatus int primary key auto_increment, descricaoStatus varchar (300) not null);

create table usuario ( codUsuario int primary key auto_increment, nome varchar(40) not null, codStatus int, email varchar (100) not null, senha varchar (300) not null);

alter table usuario add constraint fk_Status foreign key (codStatus) REFERENCES status (codStatus)  ON UPDATE CASCADE ON DELETE CASCADE;	

create table amigo ( codAmigo int primary key auto_increment, idSolicitante int, idSolicitado int, dataSolicitacao DATETIME, dataConfirmacao DATETIME, situacao varchar(1) not null);

alter table amigo add constraint fk_Usuario foreign key (idSolicitante) REFERENCES usuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

alter table amigo add constraint fk_UsuarioAmigo foreign key (idSolicitado) REFERENCES usuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

create table mensagem ( codMensagem int primary key auto_increment, codUsuario int, descricaoMensagem varchar (300) not null, dataMensagem DATETIME);

alter table mensagem add constraint fk_UsuarioMensagem foreign key (codUsuario) REFERENCES usuario (codUsuario)  ON UPDATE CASCADE ON DELETE CASCADE;	

-- cadastro de status
insert into status (descricaoStatus) VALUES ('solteiro');
insert into status (descricaoStatus) VALUES ('em relacionamento serio');
insert into status (descricaoStatus) VALUES ('casado');

-- cadastro de usuario
insert into usuario ( nome, codStatus, email, senha) VALUES ('murilo prado', '1', 'kruzprado@hotmail.com', md5(md5('murilo')));
insert into usuario ( nome, codStatus, email, senha) VALUES ('guilherme rodrigues', '2', 'guilherme@hotmail.com', md5(md5('guilherme')));
insert into usuario ( nome, codStatus, email, senha) VALUES ('pedro', '3', 'pedro@hotmail.com', md5(md5('pedro')));

-- cadastro de amigo
insert into amigo (idSolicitante, idSolicitado, dataSolicitacao, situacao) VALUES ('1', '2', sysdate(), 'R');
insert into amigo (idSolicitante, idSolicitado, dataSolicitacao, dataConfirmacao, situacao) VALUES ('2', '3', sysdate(), sysdate(), 'A');
insert into amigo (idSolicitante, idSolicitado, dataSolicitacao, situacao) VALUES ('3', '1', sysdate(), 'P');


-- cadastro de mensagem
insert into mensagem (codUsuario, descricaoMensagem, dataMensagem) VALUES ('1', ' oi oi oi oi', sysdate());
insert into mensagem (codUsuario, descricaoMensagem, dataMensagem) VALUES ('2', ' ola ola ola', sysdate());
insert into mensagem (codUsuario, descricaoMensagem, dataMensagem) VALUES ('2', 'hahahahaha', sysdate());
insert into mensagem (codUsuario, descricaoMensagem, dataMensagem) VALUES ('3', 'mensagem do pedro', sysdate());
insert into mensagem (codUsuario, descricaoMensagem, dataMensagem) VALUES ('3', 'mensagem do pedro 2', sysdate());

-- select geral
select usuario.nome nomeUsuario, status.descricaoStatus meuStatus, amigo.codUsuarioAmigo, dadosAmigo.nome nomeAmigo, mensagem.descricaoMensagem mensagemAmigo
from usuario  
inner join status on (usuario.codStatus = status.codStatus) 
inner join amigo on (amigo.codUsuario = usuario.codUsuario) 
inner join mensagem on (amigo.codUsuarioAmigo = mensagem.codUsuario)
inner join usuario as dadosAmigo on (amigo.codUsuarioAmigo = dadosAmigo.codUsuario) 
where usuario.codUsuario = '1'; 

-- select de mensagem de amigos
select amigo.codUsuarioAmigo, mensagem.descricaoMensagem, usuario.nome, DATE_FORMAT(dataMensagem, '%d/%m/%Y - %H:%i:%S') as data from amigo inner join mensagem on (amigo.codUsuarioAmigo = mensagem.codUsuario) inner join usuario on (amigo.codUsuarioAmigo = usuario.codUsuario) where amigo.codUsuario ='2' || usuario.codUsuario ='2';

-- select de amizade
select * from amigo where (idSolicitante = '2' OR idSolicitado = '2') AND situacao = 'A';

