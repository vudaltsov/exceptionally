workflow "Lint" {
  on = "push"
  resolves = ["psalm"]
}

action "PHP-CS-Fixer" {
  uses = "docker://oskarstark/php-cs-fixer-ga"
  args = "--diff --dry-run --allow-risky=yes"
  secrets = ["GITHUB_TOKEN"]
}

action "psalm" {
  uses = "psalm"
  needs = ["PHP-CS-Fixer"]
  secrets = ["GITHUB_TOKEN"]
  args = " --diff --diff-methods"
}
