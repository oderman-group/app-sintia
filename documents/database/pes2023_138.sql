ALTER TABLE mobiliar_dev_2023.academico_matriculas ADD mat_tipo_matricula varchar(45) DEFAULT 'grupal' NOT NULL COMMENT 'Se requiere para definir si el estudiante podrá estar en varios cursos a la vez';

ALTER TABLE mobiliar_dev_2023.academico_grados ADD gra_tipo varchar(45) DEFAULT 'grupal' NOT NULL;