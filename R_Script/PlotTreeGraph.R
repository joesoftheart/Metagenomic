args<-commandArgs(TRUE)
# Load package
library(ape)

# Create data
t <- read.tree(file=args[1])
plot(t)
png(args[2],width=10, height=8, units="in", res=300)
dev.off()
#edgelabels(t$edge.length, bg="black", col="white", font=2)