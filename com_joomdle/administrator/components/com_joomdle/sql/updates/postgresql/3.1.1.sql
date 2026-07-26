CREATE TABLE IF NOT EXISTS "#__joomdle_sso_tickets" (
  "id" serial,
  "token_hash" varchar(255) NOT NULL,
  "user_id" int NOT NULL,
  "created" bigint DEFAULT NULL,
  "consumed" bigint DEFAULT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "idx_joomdle_sso_tickets_token_hash" UNIQUE ("token_hash")
);

CREATE INDEX IF NOT EXISTS "idx_joomdle_sso_tickets_created"
  ON "#__joomdle_sso_tickets" ("created");
