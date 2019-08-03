workflow "Lint" {
  on = "push"
  resolves = [
    "phpunit",
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
