workflow "Lint" {
  on = "push"
  resolves = [
    "phpunit",
    "psalm",
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
