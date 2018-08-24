args <- commandArgs(TRUE)
#rm(list=ls())
library(calibrate)
library(shape)
#you can use in nmds or pcoa
nmds <- read.table(file = args[1], header = T)

#For OTU (final.tx.2.subsample.spearman(pearson).corr.axes that are mapped with final.tx.2.cons.taxonomy)

#png(args[2], width = 12, height = 6, units = "in", res = 300)
svg(args[2],width=12,height=6)

#plot(nmds$axis1,nmds$axis2, col=c("#0000FF","green","red","cyan"),pch=20, xlab="Axis 1", ylab="Axis 2", xlim = c(-1.0,1.0), ylim=c(-1.0,1.0), cex = 2.0)
plot(nmds$axis1, nmds$axis2, col = c(sample(colors())), pch = 20, xlab = "Axis 1", ylab = "Axis 2", xlim = c(- 1.0, 1.0), ylim = c(- 1.0, 1.0), cex = 2.0)

with(nmds, text(x = nmds$axis1, y = nmds$axis2, labels = nmds$group, pos = 4, cex = 0.8))
points <- read.table(args[3], header = T)
x1 <- points$axis1
y1 <- points$axis2
labels <- points$OTU
Arrows(0, 0, x1, y1, code = 2, arr.length = 0.2, arr.width = 0.2, arr.type = "triangle", arr.adj = 1, col = "gray")
textxy(x1, y1, labels, cex = 0.8, col = "red", offset = 0.3, pos = 4)
dev.off()


