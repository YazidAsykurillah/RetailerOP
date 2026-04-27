# Deposit Module — Feature Proposal

## Overview

The **Deposit Module** allows customers to pre-load money into a store-managed balance (a "deposit"). This balance can then be used as a payment method when creating or settling a transaction — fully or partially — reducing the outstanding balance accordingly.

> [!IMPORTANT]
> Deposit is **only applicable to registered customers**. Walk-in / guest transactions cannot use deposits. In POS, the cashier must select a registered customer before the deposit option becomes available.

---

## Permissions

This module uses **Spatie Laravel Permission** (already installed in the project). New permissions will be added via a dedicated seeder — following the same pattern as the existing `AddTransactionDeletePermissionSeeder`.

### New Permissions

| Permission Name | Description | Default Roles |
|---|---|---|
| `Manage Deposits` | Top-up and view deposit history for customers | Super Admin, Admin |
| `Use Deposit` | Apply deposit balance when processing a transaction / POS checkout | Super Admin, Admin, Cashier |

> [!NOTE]
> `Use Deposit` is separate from `Manage Deposits` so that a **Cashier** can apply an existing deposit during checkout, but cannot top-up, edit, or refund deposit balances.

### New Seeder: `AddDepositPermissionsSeeder`

Following the existing pattern, a new standalone seeder will be created:

```php
// database/seeders/AddDepositPermissionsSeeder.php
$manageDeposit = Permission::firstOrCreate(['name' => 'Manage Deposits']);
$useDeposit    = Permission::firstOrCreate(['name' => 'Use Deposit']);

// Super Admin & Admin: full deposit access
foreach (['Super Admin', 'Admin'] as $role) {
    Role::where('name', $role)->first()?->givePermissionTo([$manageDeposit, $useDeposit]);
}

// Cashier: can only use (apply) deposits, not manage them
Role::where('name', 'Cashier')->first()?->givePermissionTo($useDeposit);
```

### Route / Controller Guards

```php
// DepositController — gate at controller level
public function store(...)  { abort_if(!auth()->user()->can('Manage Deposits'), 403); ... }
public function index(...)  { abort_if(!auth()->user()->can('Manage Deposits'), 403); ... }

// POSController / TransactionController — gate on deposit payment step
if ($request->payment_method === 'deposit') {
    abort_if(!auth()->user()->can('Use Deposit'), 403);
}
```

---

## Core Concepts

| Term | Description |
|---|---|
| **Deposit** | An amount of money a customer pre-loads into their account at the store |
| **Deposit Balance** | The current usable balance a customer holds |
| **Deposit Top-up** | Adding money to a customer's deposit balance |
| **Deposit Usage** | Deducting from the balance when applied to a transaction |

---

## Proposed User Flows

### 1 — Top-up Flow (Admin adds deposit for a customer)
```
Admin → Customer List → Customer Detail
     → "Deposit" tab / section
     → Click "Top Up"
     → Enter amount, notes, payment method (how customer paid the deposit)
     → Confirm → Balance increases
```

> If an admin enters the wrong amount, they can **edit or delete** the top-up record from the deposit history table. The customer's `deposit_balance` is recalculated accordingly.

### 2 — Use Deposit at POS / New Transaction
```
POS Checkout
  → Select registered Customer (deposit option hidden for guests)
  → If customer has deposit_balance > 0: "Deposit Balance: Rp X" shown
  → Cashier ticks "Use Deposit" and enters amount (up to min(balance, grand_total))
  → Remaining amount (if any) paid by another method (cash, transfer, etc.)
  → Deposit balance is deducted, transaction recorded with two payment rows
```

### 3 — Use Deposit on Existing Transaction (Partial Payment Scenario)
```
Transaction Detail → "Add Payment"
  → Payment method: "Deposit"
  → System shows customer's current deposit balance for reference
  → System validates amount ≤ customer.deposit_balance
  → Balance deducted, payment recorded
```

---

## Database Design

### New Table: `customer_deposits`

This table records **every movement** of the deposit balance — top-ups and usages — acting as a full ledger.

| Column | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `customer_id` | FK → customers | The customer this deposit belongs to |
| `type` | enum(`top_up`, `usage`) | Direction of the movement |
| `amount` | decimal(12,2) | Always positive |
| `balance_before` | decimal(12,2) | Balance snapshot before this movement |
| `balance_after` | decimal(12,2) | Balance snapshot after this movement |
| `payment_method` | string nullable | For top-ups: how the customer paid (cash, transfer, etc.) |
| `transaction_id` | FK → transactions nullable | Filled when `type = usage`, links to the transaction |
| `notes` | text nullable | Optional notes |
| `processed_by` | FK → users | Admin/cashier who recorded this |
| `created_at` / `updated_at` | timestamps | |

> [!NOTE]
> **Top-up records can be edited or deleted by admins.** When a top-up is edited/deleted, the customer's `deposit_balance` must be recalculated by re-summing the ledger to keep consistency.

### Modified Table: `customers`

Add one new column to store the running balance (denormalized for fast reads):

| Column | Type | Description |
|---|---|---|
| `deposit_balance` | decimal(12,2) default 0 | Current usable deposit balance |

> [!NOTE]
> The `deposit_balance` column acts as a cache. The true source of truth is the `customer_deposits` ledger. Any discrepancy can be recalculated by re-summing all `top_up` minus all `usage` rows.

### Modified Table: `transaction_payments`

Add one optional nullable column to formally link a deposit payment back to its ledger entry:

| Column | Type | Description |
|---|---|---|
| `deposit_id` | FK → customer_deposits nullable | Set when `payment_method = 'deposit'`; links to the ledger row for auditability |

---

## Models

### New: `CustomerDeposit`

- `belongsTo(Customer::class)`
- `belongsTo(Transaction::class)` *(nullable)*
- `belongsTo(User::class, 'processed_by')`

### Modified: `Customer`

- Add `hasMany(CustomerDeposit::class)`
- Add helper accessor: `getDepositBalanceAttribute()` (reads from `deposit_balance` column)
- Add method: `topUpDeposit($amount, $method, $notes, $processedBy)` — wraps DB transaction
- Add method: `useDeposit($amount, $transactionId, $processedBy)` — validates balance and deducts
- Add method: `recalculateDepositBalance()` — re-sums ledger and updates `deposit_balance`; called after top-up edit/delete

### Modified: `Transaction`

- When a payment with `payment_method = 'deposit'` is added, the system triggers balance deduction on the associated `Customer`.

---

## Controllers & Routes

### New: `Admin\DepositController`

| Method | Route | Description |
|---|---|---|
| `index` | `GET /admin/deposits` | List all deposit movements across all customers (admin overview) |
| `store` | `POST /admin/customers/{customer}/deposits` | Top-up a customer's deposit |
| `update` | `PUT /admin/deposits/{deposit}` | Edit a top-up record (amount, notes) and recalculate balance |
| `destroy` | `DELETE /admin/deposits/{deposit}` | Delete a top-up record and recalculate balance |

### Modified: `Admin\TransactionController → addPayment()`

When `payment_method == 'deposit'`:
1. Resolve the `Customer` from the transaction.
2. Validate `customer.deposit_balance >= requested amount`.
3. Call `customer->useDeposit(...)`.
4. Create the `TransactionPayment` record as usual.
5. Call `transaction->refreshPaymentStatus()`.

### Modified: `Admin\POSController → processTransaction()`

If the checkout payload includes `use_deposit: true` and `deposit_amount > 0`:
1. Validate the customer is selected and has sufficient balance.
2. After creating the transaction, call `customer->useDeposit(...)`.
3. Create a `TransactionPayment` row with `payment_method = 'deposit'`.

---

## UI Touchpoints

### A — Customer Detail Page

A new **"Deposit"** tab (or card section) will appear on the customer detail/show page:

- Current balance badge (highlighted)
- Deposit history table (date, type, amount, balance before/after, notes, processed by) with **Edit** and **Delete** actions on top-up rows
- **"Top Up"** button → modal with fields: amount, payment method, notes

### B — POS Checkout Panel

When a registered customer is selected and their `deposit_balance > 0`:

- Show a **"Deposit Balance: Rp X"** indicator in the payment section
- Add a toggle/checkbox **"Use Deposit"**
- When enabled, show an amount input (pre-filled with `min(balance, grand_total)`)
- The remaining amount (if any) is covered by a second payment method (cash, card, transfer)
- Both payment rows are created on the same transaction

### C — Transaction Detail → Add Payment

The **"Add Payment"** modal already has a `payment_method` dropdown. We will add:
- A new option: **"Deposit"**
- When selected, show the customer's current deposit balance for reference
- Validate on submit that balance is sufficient

### D — Receipt / Print View

The `admin.transactions.print` view will show:
- The deposit amount used in this transaction (if applicable)
- The customer's remaining deposit balance after the transaction

### E — Admin Deposit Overview

A new page `GET /admin/deposits` listing all deposit movements (top-ups and usages) across all customers, similar to the existing Payments list page.

---

## Business Rules

1. **A deposit can only be used if a registered `customer_id` is attached to the transaction.** Guest/walk-in transactions cannot use deposits.
2. **Deposit balance cannot go negative.** The system must reject any usage exceeding the current balance.
3. **Admins can edit or delete a top-up record.** After any edit/delete, `recalculateDepositBalance()` is called to keep `customers.deposit_balance` in sync with the ledger.
4. **Only `top_up` rows can be edited/deleted.** `usage` rows are tied to a real transaction payment and must not be modified directly.
5. **Partial deposit usage is supported.** A customer can apply part of their balance to a transaction; the remainder is paid by another method — producing two `TransactionPayment` rows on the same transaction.
6. **Deposit usage is recorded in both** `customer_deposits` (ledger) **and** `transaction_payments` (with `deposit_id` FK) for full auditability.
7. **`balance_before` and `balance_after` are written at transaction time** (within a DB transaction) to prevent race conditions.
8. **The printed receipt shows the deposit amount used and the customer's remaining balance** after the transaction.

---

## Resolved Decisions

| # | Question | Decision |
|---|---|---|
| Q1 | Deposit for guest customers? | ✅ No — registered customers only; POS requires customer selection |
| Q2 | Can a wrong top-up be corrected? | ✅ Yes — admins can edit or delete top-up records; balance recalculates automatically |
| Q3 | Include Refund feature in v1? | ✅ No — deferred to a future release |
| Q4 | Partial deposit usage? | ✅ Yes — use deposit for part of the total, pay remainder by another method |
| Q5 | Deposit info on printed receipt? | ✅ Yes — show deposit amount used and remaining balance after transaction |

---

## Affected Files

### Backend

| File | Action | Notes |
|---|---|---|
| `database/migrations/..._add_deposit_balance_to_customers_table.php` | **New** | Adds `deposit_balance` column |
| `database/migrations/..._create_customer_deposits_table.php` | **New** | Ledger table |
| `database/migrations/..._add_deposit_id_to_transaction_payments_table.php` | **New** | Nullable FK to ledger |
| `database/seeders/AddDepositPermissionsSeeder.php` | **New** | Seeds `Manage Deposits` + `Use Deposit` permissions |
| `app/Models/CustomerDeposit.php` | **New** | Ledger model |
| `app/Models/Customer.php` | **Modify** | Add relationship + `topUpDeposit()`, `useDeposit()`, `recalculateDepositBalance()` |
| `app/Models/TransactionPayment.php` | **Modify** | Add `deposit_id` to fillable + `belongsTo(CustomerDeposit)` |
| `app/Http/Controllers/Admin/DepositController.php` | **New** | `index`, `store`, `update`, `destroy` |
| `app/Http/Controllers/Admin/TransactionController.php` | **Modify** | `addPayment()` — handle deposit method |
| `app/Http/Controllers/Admin/POSController.php` | **Modify** | `processTransaction()` — handle deposit usage |
| `routes/web.php` | **Modify** | Add deposit routes |

### Frontend

| File | Action | Notes |
|---|---|---|
| `resources/views/admin/customers/show.blade.php` | **Modify** | Add Deposit tab with history table + Top Up modal + Edit/Delete actions |
| `resources/views/admin/transactions/show.blade.php` | **Modify** | Deposit option in Add Payment modal |
| `resources/views/admin/transactions/print.blade.php` | **Modify** | Show deposit used + remaining balance |
| `resources/views/admin/pos/index.blade.php` | **Modify** | Deposit toggle + amount input in checkout panel |
| `resources/views/admin/deposits/index.blade.php` | **New** | Deposit movements overview page |
