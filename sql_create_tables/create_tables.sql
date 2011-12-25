create sequence USERS_SEQ
start with 1
increment by 1
nocache
nocycle
nomaxvalue;

create sequence MESSAGES_SEQ
start with 1
increment by 1
nocache
nocycle
nomaxvalue;

create sequence TOPICS_SEQ
start with 1
increment by 1
nocache
nocycle
nomaxvalue;

create sequence BRANCHES_SEQ
start with 1
increment by 1
nocache
nocycle
nomaxvalue;

create table USERS (
USER_ID number primary key,
USERNAME varchar2(200) unique not null,
PASSWORD varchar2(200) not null,
INFO varchar2 (300));

create table BRANCHES (
BRANCH_ID number primary key,
BRANCH_NAME varchar2(200) unique not null);

create table TOPICS (
TOPIC_ID number primary key ,
TOPIC_NAME varchar2(200) not null,
BRANCH_ID number,
foreign key (BRANCH_ID) references BRANCHES(BRANCH_ID));

create table MESSAGES (
MSG_ID number primary key,
MSG_TEXT varchar2(4000) not null,
MSG_TIME date,
USER_ID number,
TOPIC_ID number,
foreign key (USER_ID) references USERS(USER_ID),
foreign key (TOPIC_ID) references TOPICS(TOPIC_ID));

create or replace trigger USERS_BI 
before insert on USERS for each row
begin
	if :new.USER_ID is null then
		select USERS_SEQ.nextval
		into :new.USER_ID
		from dual;
	end if;
end;
/

create or replace trigger BRANCHES_BI 
before insert on BRANCHES for each row
begin
	if :new.BRANCH_ID is null then
		select BRANCHES_SEQ.nextval
		into :new.BRANCH_ID
		from dual;
	end if;
end;
/

create or replace trigger TOPICS_BI 
before insert on TOPICS for each row
begin
	if :new.TOPIC_ID is null then
		select TOPICS_SEQ.nextval
		into :new.TOPIC_ID
		from dual;
	end if;
end;
/

create or replace trigger MESSAGES_BI 
before insert on MESSAGES for each row
begin
	if :new.MSG_ID is null then
		select MESSAGES_SEQ.nextval
		into :new.MSG_ID
		from dual;
	end if;
end;
/
