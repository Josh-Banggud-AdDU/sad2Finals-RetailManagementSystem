const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"profile.edit":{"uri":"profile","methods":["GET","HEAD"]},"profile.update":{"uri":"profile","methods":["PATCH"]},"profile.destroy":{"uri":"profile","methods":["DELETE"]},"register":{"uri":"register","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"]},"password.email":{"uri":"forgot-password","methods":["POST"]},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"],"parameters":["token"]},"password.store":{"uri":"reset-password","methods":["POST"]},"verification.notice":{"uri":"verify-email","methods":["GET","HEAD"]},"verification.verify":{"uri":"verify-email\/{id}\/{hash}","methods":["GET","HEAD"],"parameters":["id","hash"]},"verification.send":{"uri":"email\/verification-notification","methods":["POST"]},"password.confirm":{"uri":"confirm-password","methods":["GET","HEAD"]},"password.update":{"uri":"password","methods":["PUT"]},"logout":{"uri":"logout","methods":["POST"]},"employees.index":{"uri":"employees","methods":["GET","HEAD"]},"employees.create":{"uri":"employees\/create","methods":["GET","HEAD"]},"employees.store":{"uri":"employees\/store","methods":["POST"]},"employees.edit":{"uri":"employees\/{employee}\/edit","methods":["GET","HEAD"],"parameters":["employee"],"bindings":{"employee":"id"}},"employees.update":{"uri":"employees\/{employee}","methods":["PUT","PATCH"],"parameters":["employee"]},"employees.destroy":{"uri":"employees\/{employee}","methods":["DELETE"],"parameters":["employee"]},"finances.index":{"uri":"finances\/index","methods":["GET","HEAD"]},"finances.create":{"uri":"finances\/create","methods":["GET","HEAD"]},"finances.store":{"uri":"finances","methods":["POST"]},"finances.edit":{"uri":"finances\/{finance}\/edit","methods":["GET","HEAD"],"parameters":["finance"]},"finances.update":{"uri":"finances\/{finance}","methods":["PUT","PATCH"],"parameters":["finance"]},"finances.destroy":{"uri":"finances\/{finance}","methods":["DELETE"],"parameters":["finance"]},"finances.main":{"uri":"finances\/main","methods":["GET","HEAD"]},"finances.print":{"uri":"finances\/print","methods":["GET","HEAD"]},"cash_on_hands.index":{"uri":"cash_on_hands","methods":["GET","HEAD"]},"cash_on_hands.start":{"uri":"cash_on_hands\/start","methods":["GET","HEAD"]},"cash_on_hands.end":{"uri":"cash_on_hands\/end","methods":["GET","HEAD"]},"cash_on_hands.store":{"uri":"cash_on_hands\/store","methods":["POST"]},"cash_on_hands.update":{"uri":"cash_on_hands\/update","methods":["POST"]},"cash_on_hands.edit":{"uri":"cash_on_hands\/edit","methods":["POST"]},"cashflows.create":{"uri":"cashflows\/create","methods":["GET","HEAD"]},"cashflows.store":{"uri":"cashflows\/store","methods":["POST"]},"cashflows.edit":{"uri":"cashflows\/{id}\/edit","methods":["GET","HEAD"],"parameters":["id"]},"cashflows.update":{"uri":"cashflows\/{id}","methods":["PUT"],"parameters":["id"]},"inventories.index":{"uri":"inventories","methods":["GET","HEAD"]},"inventories.create":{"uri":"inventories\/create","methods":["GET","HEAD"]},"inventories.store":{"uri":"inventories","methods":["POST"]},"inventories.edit":{"uri":"inventories\/{inventory}\/edit","methods":["GET","HEAD"],"parameters":["inventory"],"bindings":{"inventory":"id"}},"inventories.update":{"uri":"inventories\/{inventory}","methods":["PUT","PATCH"],"parameters":["inventory"]},"inventories.destroy":{"uri":"inventories\/{inventory}","methods":["DELETE"],"parameters":["inventory"]},"inventories.main":{"uri":"inventories\/main","methods":["GET","HEAD"]},"inventories.manage_supply":{"uri":"inventories\/manage_supply","methods":["GET","HEAD"]},"sales.index":{"uri":"sales","methods":["GET","HEAD"]},"sales.create":{"uri":"sales\/create","methods":["GET","HEAD"]},"sales.store":{"uri":"sales\/store","methods":["POST"]},"sales.edit":{"uri":"sales\/{sale}\/edit","methods":["GET","HEAD"],"parameters":["sale"]},"sales.update":{"uri":"sales\/{sale}","methods":["PUT","PATCH"],"parameters":["sale"]},"sales.destroy":{"uri":"sales\/{sale}","methods":["DELETE"],"parameters":["sale"]},"sales.main":{"uri":"sales\/main","methods":["GET","HEAD"]},"sales_items.store":{"uri":"sales_items\/store","methods":["POST"]},"supply_management.index":{"uri":"supply_management","methods":["GET","HEAD"]},"supply_management.create":{"uri":"supply_management\/create","methods":["GET","HEAD"]},"supply_management.store":{"uri":"supply_management","methods":["POST"]},"supply_management.edit":{"uri":"supply_management\/{supply_management}\/edit","methods":["GET","HEAD"],"parameters":["supply_management"]},"supply_management.update":{"uri":"supply_management\/{supply_management}","methods":["PUT","PATCH"],"parameters":["supply_management"]},"supply_management.destroy":{"uri":"supply_management\/{supply_management}","methods":["DELETE"],"parameters":["supply_management"]},"inventory_logs.stock_in":{"uri":"supply_management\/stock_in","methods":["GET","HEAD"]},"inventory_logs.stock_out":{"uri":"supply_management\/stock_out","methods":["GET","HEAD"]},"inventory_logs.item_return":{"uri":"supply_management\/item_return","methods":["GET","HEAD"]},"inventory_logs.store":{"uri":"supply_management\/store","methods":["POST"]},"inventory_logs_items.store":{"uri":"supply_management_items\/store","methods":["POST"]},"suppliers.index":{"uri":"suppliers","methods":["GET","HEAD"]},"suppliers.create":{"uri":"suppliers\/create","methods":["GET","HEAD"]},"suppliers.store":{"uri":"suppliers","methods":["POST"]},"suppliers.show":{"uri":"suppliers\/{supplier}","methods":["GET","HEAD"],"parameters":["supplier"]},"suppliers.edit":{"uri":"suppliers\/{supplier}\/edit","methods":["GET","HEAD"],"parameters":["supplier"]},"suppliers.update":{"uri":"suppliers\/{supplier}","methods":["PUT","PATCH"],"parameters":["supplier"]},"suppliers.destroy":{"uri":"suppliers\/{supplier}","methods":["DELETE"],"parameters":["supplier"]},"services.index":{"uri":"services\/index","methods":["GET","HEAD"]},"services.store":{"uri":"services\/store","methods":["POST"]},"job_orders.index":{"uri":"job_orders","methods":["GET","HEAD"]},"job_orders.create":{"uri":"job_orders\/create","methods":["GET","HEAD"]},"job_orders.store":{"uri":"job_orders\/store","methods":["POST"]},"job_orders.edit":{"uri":"job_orders\/edit\/{jobOrder}","methods":["GET","HEAD"],"parameters":["jobOrder"],"bindings":{"jobOrder":"id"}},"job_orders.update":{"uri":"job_orders\/update","methods":["POST"]},"job_orders.destroy":{"uri":"job_orders\/{job_order}","methods":["DELETE"],"parameters":["job_order"]},"job_orders.main":{"uri":"job_orders\/main","methods":["GET","HEAD"]},"job_orders.history":{"uri":"job_orders\/history","methods":["GET","HEAD"]},"job_order_items.store":{"uri":"job_order_items\/store","methods":["POST"]},"job_order_items.update":{"uri":"job_order_items\/update","methods":["POST"]},"job_order_services.store":{"uri":"job_order_services\/store","methods":["POST"]},"job_order_services.update":{"uri":"job_order_services\/update","methods":["POST"]},"job_order_employees.store":{"uri":"job_order_employees\/store","methods":["POST"]},"job_order_employees.update":{"uri":"job_order_employees\/update","methods":["POST"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
