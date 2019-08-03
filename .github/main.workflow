workflow "Lint" {
  on = "push"
  resolves = [
    "phpunit",
  ]
}

action "composer" {
  uses = "MilesChou/composer-action@master"
  args = "install -q --no-ansi --no-interaction --no-scripts --no-suggest --no-progress --prefer-dist"
}

action "phpunit" {
  needs = ["composer"]
  uses = "./.github/actions/phpunit/"
  args = ""
}
