<?php

class CRM_Pwppb_Util_Db {
  
  /**
   * Check whether a string value will fit into a VARCHAR column.
   *
   * @param string $value
   * @param string $tableName
   * @param string $columnName
   *
   * @return bool
   *   TRUE if $value will fit; FALSE otherwise.
   *
   * @throws CRM_Core_Exception
   *   If the table/column is invalid, missing, or not VARCHAR.
   */
  public static function checkVarcharValueLength(string $value, string $tableName, string $columnName): bool {

    static $cache = [];

    $cacheKey = "{$tableName}.{$columnName}";

    if (!isset($cache[$cacheKey])) {
      // Extremely conservative identifier validation.
      // Prevents SQL injection since identifiers cannot be parameterized.
      foreach ([$tableName, $columnName] as $identifier) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
          throw new CRM_Core_Exception(
            "Invalid SQL identifier: {$identifier}"
          );
        }
      }

      // Get schema details for this table.column.
      $query = "
        SELECT
          DATA_TYPE,
          CHARACTER_MAXIMUM_LENGTH
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = %1
          AND COLUMN_NAME = %2
      ";

      $dao = CRM_Core_DAO::executeQuery($query, [
        1 => [$tableName, 'String'],
        2 => [$columnName, 'String'],
      ]);

      if (!$dao->fetch()) {
        throw new CRM_Core_Exception(
          "Unable to determine schema for {$tableName}.{$columnName}"
        );
      }

      if (strtolower($dao->DATA_TYPE) !== 'varchar') {
        throw new CRM_Core_Exception(
          "{$tableName}.{$columnName} is not a VARCHAR column"
        );
      }

      $maxLength = $dao->CHARACTER_MAXIMUM_LENGTH;

      if (!is_numeric($maxLength) || $maxLength <= 0) {
        throw new CRM_Core_Exception(
          "Invalid VARCHAR length for {$tableName}.{$columnName}"
        );
      }

      $cache[$cacheKey] = (int) $maxLength;
    }

    $maxLength = $cache[$cacheKey];

    return (mb_strlen($value, 'UTF-8') <= $maxLength);
  }  
}