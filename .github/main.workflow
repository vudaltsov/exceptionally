workflow "Lint" {
  on = "push"
  resolves = ["phpunit"]
}

action "composer" {
  uses = "MilesChou/composer-action@master"
  runs = "install"
  secrets = ["GITHUB_TOKEN"]
}

action "phpunit" {
  uses = "./.github/actions/phpunit"
  needs = ["composer"]
  secrets = ["GITHUB_TOKEN"]
}
