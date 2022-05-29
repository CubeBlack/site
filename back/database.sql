CREATE TABLE IF NOT EXISTS `projeto_arquivo` ( 
    `codigo` INT NOT NULL AUTO_INCREMENT , 
    `projeto` INT NOT NULL ,
    `caminho` VARCHAR(255) NOT NULL ,
    `checksum` VARCHAR(100) NOT NULL ,
    PRIMARY KEY (`codigo`)
) ENGINE = InnoDB; 

INSERT projeto_arquivo SET
projeto = 0,
caminho = 'c:/a',
checksum = 'checksum'
;

INSERT projeto_arquivo SET
projeto = 0,
caminho = 'c:/b',
checksum = 'checksum'
;
