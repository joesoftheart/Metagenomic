args<-commandArgs(TRUE)
# Load package
library(ape)
# Create data
t <- read.tree(file=args[1])
png(args[2],width=8, height=6, units="in", res=300)
plot(t)
#edgelabels(t$edge.length, bg="black", col="white", font=2)
dev.off()