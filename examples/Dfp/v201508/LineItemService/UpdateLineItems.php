<?php
/**
 * This example updates a standard line item's priority to high. To
 * determine which line items exist, run GetAllLineItems.php.
 *
 * PHP version 5
 *
 * Copyright 2014, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsDfp
 * @subpackage v201508
 * @category   WebServices
 * @copyright  2014, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 */
error_reporting(E_STRICT | E_ALL);

// You can set the include path to src directory or reference
// DfpUser.php directly via require_once.
// $path = '/path/to/dfp_api_php_lib/src';
$path = dirname(__FILE__) . '/../../../../src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Google/Api/Ads/Dfp/Lib/DfpUser.php';
require_once 'Google/Api/Ads/Dfp/Util/v201508/StatementBuilder.php';
require_once dirname(__FILE__) . '/../../../Common/ExampleUtils.php';

// Set the ID of the line item to update.
$lineItemId = 'INSERT_LINE_ITEM_ID_HERE';

try {
  // Get DfpUser from credentials in "../auth.ini"
  // relative to the DfpUser.php file's directory.
  $user = new DfpUser();

  // Log SOAP XML request and response.
  $user->LogDefaults();

  // Get the LineItemService.
  $lineItemService = $user->GetService('LineItemService', 'v201508');

  // Create a statement to select a single line item by ID.
  $statementBuilder = new StatementBuilder();
  $statementBuilder->Where('id = :id')
      ->OrderBy('id ASC')
      ->Limit(1)
      ->WithBindVariableValue('id', $lineItemId);

  // Get the line item.
  $page = $lineItemService->getLineItemsByStatement(
      $statementBuilder->ToStatement());
  $lineItem = $page->results[0];

  // Update the line item's priority to high if possible.
  if ($lineItem->lineItemType === 'STANDARD') {
    $lineItem->priority = 6;

    // Update the line item on the server.
    $lineItems = $lineItemService->updateLineItems(array($lineItem));

    foreach ($lineItems as $updatedLineItem) {
      printf("Line item with ID %d, name '%s' was updated.\n",
          $updatedLineItem->id, $updatedLineItem->name);
    }
  } else {
    printf('No line items were updated.');
  }
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("%s\n", $e->getMessage());
}

