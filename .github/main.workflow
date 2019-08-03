workflow "main" {
  on = "push"
  resolves = [
    "phpunit",
    "psalm",
    "php-cs-fixer",
  ]
}

action "composer" {
  uses = "MilesChou/composer-action@master"
  args = "update"
}

action "phpunit" {
  needs = ["composer"]
  uses = "./actions/phpunit/"
}

action "psalm" {
  uses = "./actions/psalm/"
  needs = ["composer"]
}

action "php-cs-fixer" {
  uses = "./actions/php-cs-fixer/"
  needs = ["composer"]
  args = "--allow-risky=yes --dry-run --diff --diff-format=udiff -vv"
}
