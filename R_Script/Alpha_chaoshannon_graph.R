args <- commandArgs(TRUE)
library(ggplot2)
library(grid)
data = read.table(args[1], sep = "\t", header = T)
data$source <- as.factor(data$Source)
change_names <- c(
`Chao` = "Chao",
`Shannon` = "Shannon"
)
p <- ggplot(data, aes(x = Source, y = value, fill = Source)) +
    stat_boxplot(geom = 'errorbar') +
    geom_boxplot() +
## Divide by levels of "sex", in the horizontal direction
    facet_wrap(~ result, scales = "free", labeller = as_labeller(change_names)) +
## Divide by levels of "sex", in the vertical direction
#facet_grid(result~., scales="free_y")+
    scale_y_continuous(name = "") +
    scale_x_discrete(name = "") +
    scale_fill_manual(values = c(sample(colors()))) +
    theme_bw() +
    theme(text = element_text(family = "Arial", size = 18),
    axis.text.x = element_text(colour = "black", angle = 45, hjust = 1, size = 16),
    axis.line = element_line(size = 0.1, colour = "black"),
    legend.title = element_text(size = 16, face = 'bold'),
    legend.text = element_text(size = 14),
    strip.text.x = element_text(size = 16, angle = 0, face = 'bold'))#,

#png(args[2], width = 10, height = 8, units = "in", res = 300)
svg(args[2], width = 10, height = 8)

p + theme(panel.spacing = unit(1, "lines"))
dev.off()
