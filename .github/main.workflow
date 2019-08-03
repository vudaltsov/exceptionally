workflow "Lint" {
  on = "push"
  resolves = [
    "phpunit",
    "phpunit2",
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

action "phpunit2" {
  uses = "./actions/phpunit/"
  needs = ["composer"]
}
