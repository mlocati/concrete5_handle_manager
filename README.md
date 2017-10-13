```sql
CREATE TABLE HandleUsages (
  handle varchar(64) NOT NULL COMMENT 'The package handle',
  system_prb tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Handle used in: concrete5 marketplace',
  system_translate tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Handle used in: translate.concrete5.org',
  PRIMARY KEY (handle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of package handle usages';
```
