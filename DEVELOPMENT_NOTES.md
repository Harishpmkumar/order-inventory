# Mallow Laravel Assignment

## Database Design

### Tables

- customers
- products
- orders
- order_items

### Relationships

- Customer hasMany Orders
- Order belongsTo Customer
- Order hasMany OrderItems
- OrderItem belongsTo Order
- OrderItem belongsTo Product
- Product hasMany OrderItems

### Design Decisions

- Customer email must be unique.
- Product code must be unique.
- Order items store unit price and tax percentage as a snapshot of the values at the time of purchase.
- Monetary values will use decimal database columns.
- An order is atomic: if any product has insufficient stock, the complete order fails.
- Stock validation and deduction will be protected using a database transaction and row-level locking.
- Low-stock threshold will be configurable.

## Assumptions

(To be filled during development.)

## Questions

(To be filled if needed.)

## Improvements

(To be filled later.)
